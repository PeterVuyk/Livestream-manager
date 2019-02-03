<?php
declare(strict_types=1);

namespace App\Tests\Service\MessageProcessor;

use App\Exception\Messaging\InvalidMessageTypeException;
use App\Messaging\Library\Command\StartLivestreamCommand;
use App\Messaging\Library\MessageInterface;
use App\Service\MessageProcessor\ProcessMessageDelegator;
use App\Service\MessageProcessor\StartLivestreamProcessor;
use App\Service\MessageProcessor\StopLivestreamProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Service\MessageProcessor\ProcessMessageDelegator
 * @covers ::<!public>
 * @covers ::__construct
 * @uses \App\Messaging\Library\Command\StartLivestreamCommand
 */
class ProcessMessageDelegatorTest extends TestCase
{
    /** @var StartLivestreamProcessor|MockObject */
    private $startLivestreamProcessor;

    /** @var StopLivestreamProcessor|MockObject */
    private $stopLivestreamProcessor;

    /** @var ProcessMessageDelegator */
    private $processMessageDelegator;

    protected function setUp()
    {
        $this->startLivestreamProcessor = $this->createMock(StartLivestreamProcessor::class);
        $this->stopLivestreamProcessor = $this->createMock(StopLivestreamProcessor::class);
        $this->processMessageDelegator = new ProcessMessageDelegator(
            $this->startLivestreamProcessor,
            $this->stopLivestreamProcessor
        );
    }

    /**
     * @covers ::process
     * @throws InvalidMessageTypeException
     */
    public function testProcessSuccess()
    {
        $this->startLivestreamProcessor->expects($this->once())->method('process');
        $this->processMessageDelegator->process(StartLivestreamCommand::create());
    }

    /**
     * @covers ::process
     * @throws InvalidMessageTypeException
     */
    public function testProcessFailed()
    {
        $this->expectException(InvalidMessageTypeException::class);
        $this->processMessageDelegator->process($this->createMock(MessageInterface::class));
    }
}
