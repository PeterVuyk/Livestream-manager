<?php
declare(strict_types=1);

namespace App\Command;

use App\Exception\CouldNotFindMainCameraException;
use App\Exception\PublishMessageFailedException;
use App\Messaging\Dispatcher\MessagingDispatcher;
use App\Service\LivestreamService;
use App\Messaging\Library\Command\StartLivestreamCommand as MessageStartLivestreamCommand;
use App\Service\StreamProcessing\StreamStateMachine;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartLivestreamCommand extends Command
{
    const COMMAND_START_STREAM = 'stream:start';

    /** @var MessagingDispatcher */
    private $messagingDispatcher;

    /** @var LoggerInterface */
    private $logger;

    /** @var LivestreamService */
    private $livestreamService;

    /** @var StreamStateMachine */
    private $streamStateMachine;

    /**
     * StartLivestreamCommand constructor.
     * @param MessagingDispatcher $messagingDispatcher
     * @param LoggerInterface $logger
     * @param LivestreamService $livestreamService
     * @param StreamStateMachine $streamStateMachine
     */
    public function __construct(
        MessagingDispatcher $messagingDispatcher,
        LoggerInterface $logger,
        LivestreamService $livestreamService,
        StreamStateMachine $streamStateMachine
    ) {
        parent::__construct();
        $this->messagingDispatcher = $messagingDispatcher;
        $this->logger = $logger;
        $this->livestreamService = $livestreamService;
        $this->streamStateMachine = $streamStateMachine;
    }

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_START_STREAM)
            ->setDescription('Start the livestream.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws CouldNotFindMainCameraException
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('Requested to start livestream.');

        $camera = $this->livestreamService->getMainCameraStatus();
        $toStarting = $this->streamStateMachine->can($camera, 'to_starting');

        if (!$toStarting) {
            $message = "tried to start livestream while this is not possible, current state: {$camera->getState()}";
            $this->logger->warning($message);
            $output->writeln("<error>{$message}</error>");
            return;
        }

        try {
            $this->messagingDispatcher->sendMessage(MessageStartLivestreamCommand::create());
        } catch (PublishMessageFailedException $exception) {
            $this->logger->error('Could not send start command livestream', ['exception' => $exception]);
            $output->writeln('Could not start livestream.');
        }
    }
}
