<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\ScheduleLog;
use App\Entity\StreamSchedule;
use App\Exception\CouldNotExecuteCommandException;
use App\Exception\CouldNotModifyStreamScheduleException;
use App\Repository\StreamScheduleRepository;
use App\Service\StartStreamService;
use App\Service\StopStreamService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SchedulerExecuteCommand extends Command
{
    const COMMAND_SCHEDULER_EXECUTE = 'scheduler:execute';

    const ERROR_MESSAGE = '<error>%s - Aborted</error>';
    const INFO_MESSAGE = '<info>%s</info>';

    /** @var StreamScheduleRepository */
    private $streamScheduleRepository;

    /** @var StartStreamService */
    private $startStreamService;

    /** @var StopStreamService */
    private $stopStreamService;

    /** @var LoggerInterface */
    private $logger;

    /**
     * SchedulerExecuteCommand constructor.
     * @param StreamScheduleRepository $streamScheduleRepository
     * @param StartStreamService $startStreamService
     * @param StopStreamService $stopStreamService
     * @param LoggerInterface $logger
     */
    public function __construct(
        StreamScheduleRepository $streamScheduleRepository,
        StartStreamService $startStreamService,
        StopStreamService $stopStreamService,
        LoggerInterface $logger
    ) {
        parent::__construct();
        $this->streamScheduleRepository = $streamScheduleRepository;
        $this->startStreamService = $startStreamService;
        $this->stopStreamService = $stopStreamService;
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_SCHEDULER_EXECUTE)
            ->setDescription('Execute scheduled commands.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf(self::INFO_MESSAGE, 'Scheduler execution started'));
        $this->processSchedules($input, $output);
        $output->writeln(sprintf(self::INFO_MESSAGE, 'Scheduler execution finished'));
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function processSchedules(InputInterface $input, OutputInterface $output)
    {
        $streamSchedules = $this->streamScheduleRepository->findActiveSchedules();
        foreach ($streamSchedules as $streamSchedule) {
            try {
                $executionEndTime = $streamSchedule->getExecutionEndTime();
                if ($executionEndTime instanceof \DateTime && $executionEndTime <= new \DateTime()) {
                    $this->executeStopStream($streamSchedule, $input, $output);
                    continue;
                }
                if ($streamSchedule->streamTobeExecuted() === true) {
                    $this->executeStartStream($streamSchedule, $input, $output);
                }
            } catch (CouldNotExecuteCommandException $exception) {
                //Do nothing, already logged. Continue process.
            } catch (\Exception $exception) {
                $this->logger->error('could not update stream schedule', ['message' => $exception->getMessage()]);
            }
        }
    }

    /**
     * @param StreamSchedule $streamSchedule
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws CouldNotExecuteCommandException
     * @throws CouldNotModifyStreamScheduleException
     */
    private function executeStopStream(StreamSchedule $streamSchedule, InputInterface $input, OutputInterface $output)
    {
        try {
            $this->stopStreamService->process();
            $streamSchedule->setIsRunning(false);
            $scheduleLog = new ScheduleLog($streamSchedule, true, 'Livestream successfully stopped');
            $streamSchedule->addScheduleLog($scheduleLog);
            $output->writeln(sprintf(self::INFO_MESSAGE, 'Livestream successfully stopped'));
        } catch (\Exception $exception) {
            $output->writeln(sprintf(self::ERROR_MESSAGE, $exception->getMessage()));
            $this->logger->error('Could not execute stopStream command', ['exception' => $exception]);
            $streamSchedule->setIsRunning(true);
            $streamSchedule->setWrecked(true);
            $scheduleLog = new ScheduleLog($streamSchedule, false, $exception->getMessage());
            $streamSchedule->addScheduleLog($scheduleLog);
            throw CouldNotExecuteCommandException::couldNotStopLivestream($exception);
        } finally {
            $this->streamScheduleRepository->save($streamSchedule);
        }
    }

    /**
     * @param StreamSchedule $streamSchedule
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws CouldNotExecuteCommandException
     * @throws CouldNotModifyStreamScheduleException
     */
    private function executeStartStream(StreamSchedule $streamSchedule, InputInterface $input, OutputInterface $output)
    {
        try {
            $this->startStreamService->process();
            $streamSchedule->setLastExecution(new \DateTime());
            $scheduleLog = new ScheduleLog($streamSchedule, true, 'Livestream successfully started');
            $streamSchedule->addScheduleLog($scheduleLog);
            $streamSchedule->setIsRunning(true);
            $output->writeln(sprintf(self::INFO_MESSAGE, 'Livestream successfully started'));
        } catch (\Exception $exception) {
            $output->writeln(sprintf(self::ERROR_MESSAGE, $exception->getMessage()));
            $this->logger->error('Could not execute startStream command', ['exception' => $exception]);
            $streamSchedule->setIsRunning(false);
            $streamSchedule->setWrecked(true);
            $scheduleLog = new ScheduleLog($streamSchedule, false, $exception->getMessage());
            $streamSchedule->addScheduleLog($scheduleLog);
            throw CouldNotExecuteCommandException::couldNotStartLivestream($exception);
        } finally {
            $this->streamScheduleRepository->save($streamSchedule);
        }
    }
}
