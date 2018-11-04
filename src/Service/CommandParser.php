<?php
declare(strict_types=1);

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\HttpKernel\KernelInterface;

class CommandParser
{
    const STREAM_NAMESPACE = 'stream';
    const PHP_MEMORY = 'php://memory';

    /** @var KernelInterface */
    private $kernel;

    /**
     * CommandParser constructor.
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getCommands()
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(
            array(
                'command' => 'list',
                '--format' => 'xml'
            )
        );

        $output = new StreamOutput(fopen(self::PHP_MEMORY, 'w+'));
        $application->run($input, $output);
        rewind($output->getStream());

        return $this->extractCommandsFromXML(stream_get_contents($output->getStream()));

    }

    /**
     * @param $xml
     * @return array
     */
    private function extractCommandsFromXML($xml)
    {
        if ($xml == '') {
            return array();
        }

        $node = new \SimpleXMLElement($xml);
        $commandsList = array();

        foreach ($node->namespaces->namespace as $namespace) {
            $namespaceId = (string)$namespace->attributes()->id;

            if ($namespaceId === self::STREAM_NAMESPACE) {
                foreach ($namespace->command as $command) {
                    $commandsList[$namespaceId][(string)$command] = (string)$command;
                }
            }
        }
        return $commandsList;
    }

}
