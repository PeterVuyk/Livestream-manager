<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\StreamSchedule;
use App\Repository\StreamScheduleRepository;
use Cron\CronExpression;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SchedulerExecuteCommand extends Command
{
    const COMMAND_START_STREAM = 'scheduler:execute';

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
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Scheduler execution started</info>');
        $streamSchedules = $this->streamScheduleRepository->findActiveCommands();
        foreach ($streamSchedules as $streamSchedule) {
            $cron = CronExpression::factory($streamSchedule->getCronExpression());
            if (!$cron instanceof CronExpression) {
                $output->writeln(
                    '<error>Invalid cron expression: ' . $streamSchedule->getCronExpression() . ' - Aborted</error>'
                );
                continue;
            }
            if ($streamSchedule->getRunWithNextExecution()) {
                $this->executeCommand($streamSchedule, $input, $output);
            }

            if ($cron->getNextRunDate() < new \DateTime()) {
                $this->executeCommand($streamSchedule, $input, $output);
            }
        }
        $output->writeln('<info>Scheduler execution finished</info>');
    }

    /**
     * @param StreamSchedule $streamSchedule
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function executeCommand(StreamSchedule $streamSchedule, InputInterface $input, OutputInterface $output)
    {
        try {
            $command = $this->getApplication()->find($streamSchedule->getCommand());
        } catch (CommandNotFoundException $exception) {
            $output->writeln('<error>Can not find command ' . $streamSchedule->getCommand() . '</error>');
            $this->logger->error(
                'Command to execute not found',
                ['message' => $exception->getMessage(), 'command' => $streamSchedule->getCommand()]
            );
            return;
        }

        $command->mergeApplicationDefinition();
        $input->bind($command->getDefinition());

        try {
            $command->run($input, $output);
            $streamSchedule->setRunWithNextExecution(false);
            $streamSchedule->setLastRunSuccessful(true);
            $streamSchedule->setLastExecution(new \DateTimeImmutable());
        } catch (\Exception $exception) {
            $output->writeln('<error>Failed running command ' . $streamSchedule->getCommand() . '</error>');
            $this->logger->error(
                'Failed running command to execute',
                ['message' => $exception->getMessage(), 'command' => $streamSchedule->getCommand()]
            );
            $streamSchedule->setWrecked(true);
        } finally {
            $this->streamScheduleRepository->save($streamSchedule);
        }
    }
}
