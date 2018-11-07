<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\StartStreamService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartLivestreamCommand extends Command
{
    const COMMAND_START_STREAM = 'stream:start';
    const MINUTES_ARGUMENT = 'minutes';

    /** @var StartStreamService */
    private $startStreamService;

    /**
     * StartLivestreamCommand constructor.
     * @param StartStreamService $stopRecurringService
     */
    public function __construct(StartStreamService $stopRecurringService)
    {
        $this->startStreamService = $stopRecurringService;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_START_STREAM)
            ->addArgument(self::MINUTES_ARGUMENT, InputArgument::OPTIONAL)
            ->setDescription('Start the livestream.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('Process to start the livestream started.');
        $this->startStreamService->process();
        $output->writeln('Livestream started.');

        $minutes = (int)$input->getArgument(self::MINUTES_ARGUMENT);
        if (!is_int($minutes) || $minutes < 0) {
            $output->writeln('Livestream started without end time');
            return;
        }

        //TODO: Make a new 'stop command' and call it here, todo once 'one time' scheduler is done'.
    }
}
