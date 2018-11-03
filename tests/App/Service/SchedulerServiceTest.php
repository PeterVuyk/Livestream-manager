<?php
declare(strict_types=1);

namespace App\Tests\App\Service;

use App\Entity\StreamSchedule;
use App\Exception\StreamScheduleNotFoundException;
use App\Repository\StreamScheduleRepository;
use App\Service\SchedulerService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SchedulerServiceTest extends TestCase
{
    /** @var SchedulerService */
    private $scheduleService;

    /** @var StreamScheduleRepository|MockObject */
    private $streamScheduleRepository;

    public function setUp()
    {
        $this->streamScheduleRepository = $this->createMock(StreamScheduleRepository::class);
        $this->scheduleService = new SchedulerService($this->streamScheduleRepository);
    }

    public function testGetAllScheduledItems()
    {
        $this->streamScheduleRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([new StreamSchedule()]);

        $result = $this->scheduleService->getAllScheduledItems();
        $this->assertInstanceOf(StreamSchedule::class, $result[0]);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testSave()
    {
        $this->streamScheduleRepository->expects($this->once())->method('save');
        $this->scheduleService->saveStream(new StreamSchedule());
        $this->addToAssertionCount(1);
    }

    public function testGetScheduleById()
    {
        $this->streamScheduleRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(new StreamSchedule());

        $result = $this->scheduleService->getScheduleById('id');
        $this->assertInstanceOf(StreamSchedule::class, $result);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws StreamScheduleNotFoundException
     */
    public function testToggleDisablingSchedule()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setDisabled(false);
        $this->streamScheduleRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($streamSchedule);
        $this->streamScheduleRepository->expects($this->once())->method('save');

        $this->scheduleService->toggleDisablingSchedule('id');
        $this->addToAssertionCount(1);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws StreamScheduleNotFoundException
     */
    public function testToggleScheduleWithNextExecution()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setDisabled(false);
        $this->streamScheduleRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($streamSchedule);
        $this->streamScheduleRepository->expects($this->once())->method('save');

        $this->scheduleService->executeScheduleWithNextExecution('id');
        $this->addToAssertionCount(1);
    }

    /**
     * @throws ORMException
     * @throws StreamScheduleNotFoundException
     */
    public function testRemoveSchedule()
    {
        $this->streamScheduleRepository->expects($this->once())
            ->method('getScheduleById')
            ->willReturn(new StreamSchedule());

        $this->scheduleService->removeSchedule('id');
        $this->addToAssertionCount(1);
    }
}
