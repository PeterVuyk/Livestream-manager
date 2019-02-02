<?php
declare(strict_types=1);

namespace App\Command;

use App\Exception\MessagingQueueConsumerException;
use App\Messaging\Consumer\MessagingConsumer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsumeMessagesCommand extends Command
{
    const COMMAND_STREAM_MESSAGES = 'stream:consume';

    /** @var MessagingConsumer */
    private $messagingConsumer;

    /** @var LoggerInterface */
    private $logger;

    /**
     * ConsumeMessagesCommand constructor.
     * @param MessagingConsumer $messagingConsumer
     * @param LoggerInterface $logger
     */
    public function __construct(MessagingConsumer $messagingConsumer, LoggerInterface $logger)
    {
        parent::__construct();
        $this->messagingConsumer = $messagingConsumer;
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_STREAM_MESSAGES)
            ->setDescription('Consume messages from the SQS queue.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('Start process consuming messages.');

        try {
            $this->messagingConsumer->consume();
        } catch (MessagingQueueConsumerException $exception) {
            $this->logger->error('Could not consume messages from queue', ['exception' => $exception]);
            $output->writeln("<error>Failed consuming messages, error: {$exception->getMessage()}.</error>");
            return;
        }

        $output->writeln('Finished process consuming messages.');
    }
}
