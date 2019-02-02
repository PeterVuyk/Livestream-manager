<?php
declare(strict_types=1);

namespace App\Command;

use App\Exception\CouldNotFindMainCameraException;
use App\Exception\PublishMessageFailedException;
use App\Messaging\Dispatcher\MessagingDispatcher;
use App\Messaging\Library\Command\StopLivestreamCommand as MessageStopLivestreamCommand;
use App\Service\LivestreamService;
use App\Service\StreamProcessing\StreamStateMachine;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StopLivestreamCommand extends Command
{
    const COMMAND_STOP_LIVESTREAM = 'app:livestream-stop';

    /** @var MessagingDispatcher */
    private $messagingDispatcher;

    /** @var LoggerInterface */
    private $logger;

    /** @var LivestreamService */
    private $livestreamService;

    /** @var StreamStateMachine */
    private $streamStateMachine;

    /**
     * StopLivestreamCommand constructor.
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
            ->setName(self::COMMAND_STOP_LIVESTREAM)
            ->setDescription('Stop the livestream.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws CouldNotFindMainCameraException
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('Requested to stop livestream.');

        $camera = $this->livestreamService->getMainCameraStatus();
        $toStopping = $this->streamStateMachine->can($camera, 'to_stopping');

        if (!$toStopping) {
            $message = "tried to stop livestream while this is not possible, current state: {$camera->getState()}";
            $this->logger->warning($message);
            $output->writeln("<error>{$message}</error>");
            return;
        }

        try {
            $this->messagingDispatcher->sendMessage(MessageStopLivestreamCommand::create());
        } catch (PublishMessageFailedException $exception) {
            $this->logger->error('Could not send stop command livestream', ['exception' => $exception]);
            $output->writeln("<error>Could not stop livestream: {$exception->getMessage()}</error>");
        }
    }
}
