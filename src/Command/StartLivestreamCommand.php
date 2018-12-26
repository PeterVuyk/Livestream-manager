<?php
declare(strict_types=1);

namespace App\Command;

use App\Exception\CouldNotStartLivestreamException;
use App\Service\StartStreamService;
use App\Service\StatusStreamService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartLivestreamCommand extends Command
{
    const COMMAND_START_STREAM = 'stream:start';

    /** @var StartStreamService */
    private $startStreamService;

    /** @var StatusStreamService */
    private $statusStreamService;

    /**
     * StartLivestreamCommand constructor.
     * @param StartStreamService $stopStreamService
     * @param StatusStreamService $statusStreamService
     */
    public function __construct(StartStreamService $stopStreamService, StatusStreamService $statusStreamService)
    {
        $this->startStreamService = $stopStreamService;
        $this->statusStreamService = $statusStreamService;
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
     * @throws CouldNotStartLivestreamException
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
//      ->   if need to be start and stream is running:   Mark stream as running and don't perform any action.
        if ($this->statusStreamService->isRunning() === true) {
            $output->writeln('Livestream already started.');
            return;
        }

//      ->   if need to be start and stream is not running:   Start stream
        $output->writeln('Process to start the livestream started.');
        $this->startStreamService->process();
        $output->writeln('Livestream started.');
    }
}
