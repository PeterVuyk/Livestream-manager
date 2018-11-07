<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\StopStreamService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StopLivestreamCommand extends Command
{
    const COMMAND_STOP_STREAM = 'stream:stop';

    /** @var StopStreamService */
    private $stopStreamService;

    /**
     * StartLivestreamCommand constructor.
     * @param StopStreamService $stopRecurringService
     */
    public function __construct(StopStreamService $stopRecurringService)
    {
        $this->stopStreamService = $stopRecurringService;
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
        $output->writeln('Process to start the livestream started.');
        $this->stopStreamService->process();
        $output->writeln('Livestream started.');
    }
}
