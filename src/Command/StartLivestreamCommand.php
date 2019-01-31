<?php
declare(strict_types=1);

namespace App\Command;

use App\Exception\CouldNotStartLivestreamException;
use App\Service\StreamProcessing\StartStreamService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartLivestreamCommand extends Command
{
    const COMMAND_START_STREAM = 'stream:start';

    /** @var StartStreamService */
    private $startStreamService;

    /** @var LoggerInterface */
    private $logger;

    /**
     * StartLivestreamCommand constructor.
     * @param StartStreamService $startStream
     * @param LoggerInterface $logger
     */
    public function __construct(StartStreamService $startStream, LoggerInterface $logger)
    {
        $this->startStreamService = $startStream;
        $this->logger = $logger;
        parent::__construct();
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
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('Starting livestream running.');
        try {
            $this->startStreamService->process();
            $output->writeln('Livestream running.');
        } catch (CouldNotStartLivestreamException $exception) {
            $output->writeln('Failed starting livestream.');
            $this->logger->error('Could not start livestream', ['exception' => $exception]);
        }
    }
}
