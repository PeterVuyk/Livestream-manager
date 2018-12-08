<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\StatusStreamService;
use App\Service\StopStreamService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StopLivestreamCommand extends Command
{
    const COMMAND_STOP_STREAM = 'stream:stop';

    /** @var StopStreamService */
    private $stopStreamService;

    /** @var StatusStreamService */
    private $statusStreamService;

    /**
     * StartLivestreamCommand constructor.
     * @param StopStreamService $stopStreamService
     * @param StatusStreamService $statusStreamService
     */
    public function __construct(StopStreamService $stopStreamService, StatusStreamService $statusStreamService)
    {
        $this->stopStreamService = $stopStreamService;
        $this->statusStreamService = $statusStreamService;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_STOP_STREAM)
            ->setDescription('Start the livestream.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
//      ->   if need to be stopped and stream is not running: Mark stream as stopped and don't perform any action.
        if ($this->statusStreamService->isRunning() === false) {
            $output->writeln('Livestream already stoped.');
            return;
        }

//      ->   if need to be stopped and stream is running: Stop stream
        $output->writeln('Process to stop the livestream started.');
        $this->stopStreamService->process();
        $output->writeln('Livestream stoped.');
    }
}
