<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\ScheduleLog;
use App\Entity\RecurringSchedule;
use App\Exception\CouldNotExecuteCommandException;
use App\Repository\RecurringScheduleRepository;
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

    /** @var RecurringScheduleRepository */
    private $recurringScheduleRepository;

    /** @var LoggerInterface */
    private $logger;

    /**
     * SchedulerExecuteCommand constructor.
     * @param RecurringScheduleRepository $recurringScheduleRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        RecurringScheduleRepository $recurringScheduleRepository,
        LoggerInterface $logger
    ) {
        $this->recurringScheduleRepository = $recurringScheduleRepository;
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
        $recurringSchedules = $this->recurringScheduleRepository->findActiveCommands();
        foreach ($recurringSchedules as $recurringSchedule) {
            $cron = CronExpression::factory($recurringSchedule->getCronExpression());
            if (!$cron instanceof CronExpression) {
                $output->writeln(sprintf(
                    self::ERROR_MESSAGE,
                    'Invalid cron expression: ' . $recurringSchedule->getCronExpression()
                ));
                continue;
            }

            if ($cron->getNextRunDate() < new \DateTime() || $recurringSchedule->getRunWithNextExecution()) {
                try {
                    $this->executeCommand($recurringSchedule, $input, $output);
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
     * @param RecurringSchedule $recurringSchedule
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws CouldNotExecuteCommandException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function executeCommand(RecurringSchedule $recurringSchedule, InputInterface $input, OutputInterface $output)
    {
        try {
            $command = $this->getApplication()->find($recurringSchedule->getCommand());

            $command->mergeApplicationDefinition();
            $input->bind($command->getDefinition());

            $command->run($input, $output);
            $recurringSchedule->setRunWithNextExecution(false);
            $recurringSchedule->setLastExecution(new \DateTimeImmutable());
            $scheduleLog = new ScheduleLog($recurringSchedule, true, 'Command successfully executed');
            $recurringSchedule->addScheduleLog($scheduleLog);

            $output->writeln(sprintf(self::INFO_MESSAGE, 'Command successfully executed'));
        } catch (\Exception $exception) {
            $output->writeln(sprintf(self::ERROR_MESSAGE, $exception->getMessage()));
            $this->logger->error('Could not execute command', ['message' => $exception->getMessage()]);

            $recurringSchedule->setWrecked(true);
            $scheduleLog = new ScheduleLog($recurringSchedule, false, $exception->getMessage());
            $recurringSchedule->addScheduleLog($scheduleLog);
            throw CouldNotExecuteCommandException::couldNotRunCommand($recurringSchedule, $exception);
        } finally {
            $this->recurringScheduleRepository->save($recurringSchedule);
        }
    }
}
