<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\ScheduleLog;
use App\Entity\StreamSchedule;
use App\Exception\CouldNotExecuteCommandException;
use App\Repository\StreamScheduleRepository;
use Cron\CronExpression;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SchedulerExecuteCommand extends Command
{
    const COMMAND_START_STREAM = 'scheduler:execute';

    const ERROR_MESSAGE = '<error>%s - Aborted</error>';
    const INFO_MESSAGE = '<info>%s</info>';

    /** @var StreamScheduleRepository */
    private $streamScheduleRepository;

    /** @var LoggerInterface */
    private $logger;

    /**
     * SchedulerExecuteCommand constructor.
     * @param StreamScheduleRepository $streamScheduleRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        StreamScheduleRepository $streamScheduleRepository,
        LoggerInterface $logger
    ) {
        $this->streamScheduleRepository = $streamScheduleRepository;
        $this->logger = $logger;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_START_STREAM)
            ->setDescription('Execute scheduled recurring commands.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf(self::INFO_MESSAGE, 'Scheduler execution started'));
        $streamSchedules = $this->streamScheduleRepository->findActiveCommands();
        foreach ($streamSchedules as $streamSchedule) {
            $cron = CronExpression::factory($streamSchedule->getCronExpression());
            if (!$cron instanceof CronExpression) {
                $output->writeln(sprintf(
                    self::ERROR_MESSAGE,
                    'Invalid cron expression: ' . $streamSchedule->getCronExpression()
                ));
                continue;
            }

            if ($cron->getNextRunDate() < new \DateTime() || $streamSchedule->getRunWithNextExecution()) {
                try {
                    $this->executeCommand($streamSchedule, $input, $output);
                } catch (ORMException | OptimisticLockException $exception) {
                    $this->logger->error('could not update stream schedule', ['message' => $exception->getMessage()]);
                } catch (CouldNotExecuteCommandException $exception) {
                    //Do nothing, already logged. Continue process to run the next command.
                }
            }
        }
        $output->writeln(sprintf(self::INFO_MESSAGE, 'Scheduler execution finished'));
    }

    /**
     * @param StreamSchedule $streamSchedule
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws CouldNotExecuteCommandException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function executeCommand(StreamSchedule $streamSchedule, InputInterface $input, OutputInterface $output)
    {
        try {
            $command = $this->getApplication()->find($streamSchedule->getCommand());

            $command->mergeApplicationDefinition();
            $input->bind($command->getDefinition());

            $command->run($input, $output);
            $streamSchedule->setRunWithNextExecution(false);
            $streamSchedule->setLastExecution(new \DateTimeImmutable());
            $scheduleLog = new ScheduleLog($streamSchedule, true, 'Command successfully executed');
            $streamSchedule->addScheduleLog($scheduleLog);

            $output->writeln(sprintf(self::INFO_MESSAGE, 'Command successfully executed'));
        } catch (\Exception $exception) {
            $output->writeln(sprintf(self::ERROR_MESSAGE, $exception->getMessage()));
            $this->logger->error('Could not execute command', ['message' => $exception->getMessage()]);

            $streamSchedule->setWrecked(true);
            $scheduleLog = new ScheduleLog($streamSchedule, false, $exception->getMessage());
            $streamSchedule->addScheduleLog($scheduleLog);
            throw CouldNotExecuteCommandException::couldNotRunCommand($streamSchedule, $exception);
        } finally {
            $this->streamScheduleRepository->save($streamSchedule);
        }
    }
}
