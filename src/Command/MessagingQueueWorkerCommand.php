<?php
declare(strict_types=1);

namespace App\Command;

use App\Messaging\Consumer\MessagingQueueWorker;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MessagingQueueWorkerCommand extends Command
{
    const COMMAND_MESSAGING_WORKER = 'app:messaging-queue-worker';

    /** @var MessagingQueueWorker */
    private $messagingQueueWorker;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param MessagingQueueWorker $messagingQueueWorker
     * @param LoggerInterface $logger
     */
    public function __construct(MessagingQueueWorker $messagingQueueWorker, LoggerInterface $logger)
    {
        parent::__construct();
        $this->messagingQueueWorker = $messagingQueueWorker;
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_MESSAGING_WORKER)
            ->setDescription('Worker to consume messages from the SQS queue.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('Start process consuming messages.');

        ($this->messagingQueueWorker)();

        $this->logger->error('messaging queue worker stopped working');
        $output->writeln('<error>messaging queue worker stopped working</error>');
    }
}
