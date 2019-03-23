<?php
declare(strict_types=1);

namespace App\Command;

use App\Exception\Messaging\PublishMessageFailedException;
use App\Messaging\Dispatcher\MessagingDispatcher;
use App\Messaging\Library\Command\StartLivestreamCommand as MessageStartLivestreamCommand;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartLivestreamCommand extends Command
{
    const COMMAND_START_LIVESTREAM = 'app:livestream-start';

    /** @var MessagingDispatcher */
    private $messagingDispatcher;

    /** @var LoggerInterface */
    private $logger;

    /**
     * StartLivestreamCommand constructor.
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
            ->setName(self::COMMAND_START_LIVESTREAM)
            ->setDescription('Start the livestream.')
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
        $output->writeln('Requested to start livestream.');
        $channel = $input->getArgument('channelName');
        try {
            $this->messagingDispatcher->sendMessage(MessageStartLivestreamCommand::create($channel));
        } catch (PublishMessageFailedException $exception) {
            $this->logger->error('Could not send \'start command livestream\'', ['exception' => $exception]);
            $output->writeln('<error>Could not start livestream.</error>');
        }
    }
}
