<?php
declare(strict_types=1);

namespace App\Command;

use App\Exception\Messaging\PublishMessageFailedException;
use App\Messaging\Dispatcher\MessagingDispatcher;
use App\Messaging\Library\Command\StopLivestreamCommand as MessageStopLivestreamCommand;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StopLivestreamCommand extends Command
{
    const COMMAND_STOP_LIVESTREAM = 'app:livestream-stop';

    /** @var MessagingDispatcher */
    private $messagingDispatcher;

    /** @var LoggerInterface */
    private $logger;

    /**
     * StopLivestreamCommand constructor.
     * @param MessagingDispatcher $messagingDispatcher
     * @param LoggerInterface $logger
     */
    public function __construct(
        MessagingDispatcher $messagingDispatcher,
        LoggerInterface $logger
    ) {
        parent::__construct();
        $this->messagingDispatcher = $messagingDispatcher;
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_STOP_LIVESTREAM)
            ->setDescription('Stop the livestream.')
            ->addArgument(
                'channelName',
                InputArgument::REQUIRED,
                'The name of the channel that you would like to start'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $channel = $input->getArgument('channelName');
        $output->writeln('Requested to stop livestream.');
        try {
            $this->messagingDispatcher->sendMessage(MessageStopLivestreamCommand::create($channel));
        } catch (PublishMessageFailedException $exception) {
            $this->logger->error('Could not send \'stop command livestream\'', ['exception' => $exception]);
            $output->writeln("<error>Could not stop livestream: {$exception->getMessage()}</error>");
        }
    }
}
