<?php
declare(strict_types=1);

namespace App\Command;

use App\Exception\InvalidMessageTypeException;
use App\Exception\MessagingQueueConsumerException;
use App\Messaging\Consumer\MessagingConsumer;
use App\Messaging\Library\MessageInterface;
use App\Service\MessageProcessor\ProcessMessageDelegator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsumeSingleMessageCommand extends Command
{
    const COMMAND_CONSUME_SINGLE_MESSAGE = 'app:consume-single-message';

    /** @var MessagingConsumer */
    private $messagingConsumer;

    /** @var ProcessMessageDelegator */
    private $processMessageDelegator;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param MessagingConsumer $messagingConsumer
     * @param ProcessMessageDelegator $processMessageDelegator
     * @param LoggerInterface $logger
     */
    public function __construct(
        MessagingConsumer $messagingConsumer,
        ProcessMessageDelegator $processMessageDelegator,
        LoggerInterface $logger
    ) {
        parent::__construct();
        $this->messagingConsumer = $messagingConsumer;
        $this->processMessageDelegator = $processMessageDelegator;
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_CONSUME_SINGLE_MESSAGE)
            ->setDescription('Consume a single message from the SQS queue.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('Start process consume a message.');

        try {
            $result = $this->messagingConsumer->consume();
            $message = $this->messagingConsumer->deserializeResult($result);
        } catch (MessagingQueueConsumerException $exception) {
            $this->logger->error('Could not consume message', ['exception' => $exception]);
            $output->writeln("<error>Could not consume message: {$exception->getMessage()}</error>");
            return;
        }

        if (empty($message) || !$message instanceof MessageInterface) {
            $output->writeln('No messages found.');
            return;
        }

        try {
            $this->processMessageDelegator->process($message);
        } catch (InvalidMessageTypeException $exception) {
            $this->logger->warning('Could not process message', ['exception' => $exception]);
            $output->writeln("<error>Could not process message, message: {$exception->getMessage()}</error>");
            return;
        }

        try {
            $this->messagingConsumer->delete($result);
        } catch (MessagingQueueConsumerException $exception) {
            $this->logger->error('Could not delete from queue', ['exception' => $exception]);
            $output->writeln("<error>Could not delete from queue, message: {$exception->getMessage()}</error>");
        }
        $output->writeln('Finished process.');
    }
}
