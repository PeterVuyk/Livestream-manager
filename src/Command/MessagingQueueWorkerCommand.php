<?php
declare(strict_types=1);

namespace App\Command;

use App\Messaging\Consumer\MessagingQueueWorker;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MessagingQueueWorkerCommand extends Command
{
    const COMMAND_MESSAGING_WORKER = 'app:messaging-queue-worker';
    const NUMBER_RETRIES_TO_PROCESS = 500;

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
            ->setDescription('Worker to consume messages from the SQS queue.')
            ->addArgument(
                'numberRetriesQueue',
                InputArgument::OPTIONAL,
                'A total of retries for the messaging queue'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $numberRetriesQueue = $input->getArgument('numberRetriesQueue') ?? self::NUMBER_RETRIES_TO_PROCESS;

        $output->writeln('Start process consuming messages.');
        ($this->messagingQueueWorker)((int)$numberRetriesQueue);
        $output->writeln('Messaging queue worker finished');
    }
}
