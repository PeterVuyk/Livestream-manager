<?php
declare(strict_types=1);

namespace App\Command;

use App\Exception\Messaging\PublishMessageFailedException;
use App\Messaging\Dispatcher\MessagingDispatcher;
use App\Service\StreamScheduleService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Messaging\Library\Command\StopLivestreamCommand as MessageStopLivestreamCommand;
use App\Messaging\Library\Command\StartLivestreamCommand as MessageStartLivestreamCommand;


class SchedulerExecuteCommand extends Command
{
    const COMMAND_SCHEDULER_EXECUTE = 'app:scheduler-execute';

    const ERROR_MESSAGE = '<error>%s - Aborted</error>';
    const INFO_MESSAGE = '<info>%s</info>';

    /** @var LoggerInterface */
    private $logger;

    /** @var StreamScheduleService */
    private $streamScheduleService;

    /** @var MessagingDispatcher */
    private $messagingDispatcher;

    /**
     * SchedulerExecuteCommand constructor.
     * @param LoggerInterface $logger
     * @param StreamScheduleService $streamScheduleService
     * @param MessagingDispatcher $messagingDispatcher
     */
    public function __construct(
        LoggerInterface $logger,
        StreamScheduleService $streamScheduleService,
        MessagingDispatcher $messagingDispatcher
    ) {
        $this->logger = $logger;
        $this->streamScheduleService = $streamScheduleService;
        $this->messagingDispatcher = $messagingDispatcher;
        parent::__construct();
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

        $streamSchedules = $this->streamScheduleService->getStreamsToExecute();
        if (empty($streamSchedules)) {
            $output->writeln(sprintf(self::INFO_MESSAGE, 'No schedules to be executed'));
            return;
        }

        foreach ($streamSchedules as $streamSchedule) {
            try {
                if ($streamSchedule->streamTobeStarted()) {
                    $this->messagingDispatcher->sendMessage(
                        MessageStartLivestreamCommand::create($streamSchedule->getChannel())
                    );
                }
                if ($streamSchedule->streamToBeStopped()) {
                    $this->messagingDispatcher->sendMessage(
                        MessageStopLivestreamCommand::create($streamSchedule->getChannel())
                    );
                }
            } catch (PublishMessageFailedException $exception) {
                $this->logger->error('Could not publish message', ['exception' => $exception]);
                $output->writeln(sprintf(self::ERROR_MESSAGE, 'Could not publish message'));
            }
        }
        $output->writeln(sprintf(self::INFO_MESSAGE, 'Scheduler execution command send'));
    }
}
