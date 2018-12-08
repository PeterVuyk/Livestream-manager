<?php
declare(strict_types=1);

namespace App\Tests\App\Service\ManageSchedule;

use App\Entity\StreamSchedule;
use App\Repository\StreamScheduleRepository;
use App\Service\ManageScheduleService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ManageScheduleServiceTest extends TestCase
{
    /** @var ManageScheduleService */
    private $manageScheduleService;

    /** @var MockObject|StreamScheduleRepository */
    private $streamScheduleRepository;

    public function setUp()
    {
        $this->streamScheduleRepository = $this->createMock(StreamScheduleRepository::class);
        $this->manageScheduleService = new ManageScheduleService($this->streamScheduleRepository);
    }

    public function testGetRecurringSchedules()
    {
        $this->streamScheduleRepository->expects($this->once())
            ->method('getRecurringScheduledItems')
            ->willReturn([new StreamSchedule()]);
        $scheduledItems = $this->manageScheduleService->getRecurringSchedules();
        $this->assertInstanceOf(StreamSchedule::class, $scheduledItems[0]);
    }

    public function testGetOnetimeSchedules()
    {
        $this->streamScheduleRepository->expects($this->once())
            ->method('getActiveOnetimeScheduledItems')
            ->willReturn([new StreamSchedule()]);
        $scheduledItems = $this->manageScheduleService->getOnetimeSchedules();
        $this->assertInstanceOf(StreamSchedule::class, $scheduledItems[0]);
    }

    public function testGetScheduleById()
    {
        $this->streamScheduleRepository->expects($this->once())
            ->method('getScheduledItem')
            ->willReturn(new StreamSchedule());
        $streamSchedule = $this->manageScheduleService->getScheduleById('id');
        $this->assertInstanceOf(StreamSchedule::class, $streamSchedule);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testToggleDisablingSchedule()
    {
        $this->streamScheduleRepository->expects($this->once())->method('save');
        $schedule = new StreamSchedule();
        $schedule->setDisabled(false);
        $this->manageScheduleService->toggleDisablingSchedule($schedule);
        $this->addToAssertionCount(1);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testUnwreckSchedule()
    {
        $this->streamScheduleRepository->expects($this->once())->method('save');
        $this->manageScheduleService->unwreckSchedule(new StreamSchedule());
        $this->addToAssertionCount(1);
    }

    /**
     * @throws ORMException
     */
    public function testRemoveScheduleSuccess()
    {
        $this->streamScheduleRepository->expects($this->once())->method('remove');
        $this->manageScheduleService->removeSchedule(new StreamSchedule());
        $this->addToAssertionCount(1);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testSaveScheduleSuccess()
    {
        $this->streamScheduleRepository->expects($this->once())->method('save');
        $this->manageScheduleService->saveSchedule(new StreamSchedule());
        $this->addToAssertionCount(1);
    }
}
