<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\StreamSchedule;
use App\Repository\StreamScheduleRepository;
use App\Service\StreamScheduleService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @coversDefaultClass \App\Service\StreamScheduleService
 * @covers ::<!public>
 * @covers ::__construct()
 * @uses \App\Entity\StreamSchedule
 * @uses \App\Entity\ScheduleLog
 * @uses \App\Service\StreamScheduleService
 */
class StreamScheduleServiceTest extends TestCase
{
    /** @var StreamScheduleRepository|MockObject */
    private $streamScheduleRepository;

    /** @var EntityManagerInterface|MockObject */
    private $entityManager;

    /** @var LoggerInterface|MockObject */
    private $logger;

    /** @var StreamScheduleService */
    private $streamScheduleService;

    public function setUp()
    {
        $this->streamScheduleRepository = $this->createMock(StreamScheduleRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->streamScheduleService = new StreamScheduleService(
            $this->streamScheduleRepository,
            $this->entityManager,
            $this->logger
        );
    }

    public function testMarkConflictingStreamsAsWrecked()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setExecutionTime(new \DateTime('- 1 minutes'));
        $streamSchedule->setOnetimeExecutionDate(new \DateTime('- 1 minutes'));

        $this->entityManager->expects($this->exactly(2))->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $this->streamScheduleService->markConflictingStreamsAsWrecked([$streamSchedule, $streamSchedule]);
    }

    /**
     * @covers ::getStreamsToExecute
     */
    public function testGetStreamToExecuteNothingToExecute()
    {
        $this->streamScheduleRepository->expects($this->once())
            ->method('findActiveSchedules')
            ->willReturn([]);
        $this->assertSame([], $this->streamScheduleService->getStreamsToExecute());
    }

    /**
     * @covers ::getStreamsToExecute
     */
    public function testGetStreamToExecuteOneStreamForExecution()
    {
        $this->streamScheduleRepository->expects($this->once())
            ->method('findActiveSchedules')
            ->willReturn([$this->getStreamToBeStarted()]);

        $streamSchedules = $this->streamScheduleService->getStreamsToExecute();
        $this->assertInstanceOf(StreamSchedule::class, $streamSchedules[0]);
    }

    /**
     * @throws \Exception
     * @covers ::getStreamsToExecute
     */
    public function testGetStreamToExecuteConflictingStreams()
    {
        $this->streamScheduleRepository->expects($this->once())
            ->method('findActiveSchedules')
            ->willReturn([$this->getStreamToBeStarted(), $this->getStreamToBeStarted()]);

        $this->logger->expects($this->atLeastOnce())->method('warning');

        $this->streamScheduleService->getStreamsToExecute();
    }

    private function getStreamToBeStarted(): StreamSchedule
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setChannel('channel');
        $streamSchedule->setExecutionTime(new \DateTime('- 1 minutes'));
        $streamSchedule->setOnetimeExecutionDate(new \DateTime('- 1 minutes'));
        return $streamSchedule;
    }
}
