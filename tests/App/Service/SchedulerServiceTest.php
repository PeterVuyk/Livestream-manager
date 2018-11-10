<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\RecurringSchedule;
use App\Exception\RecurringScheduleNotFoundException;
use App\Repository\RecurringScheduleRepository;
use App\Service\SchedulerService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SchedulerServiceTest extends TestCase
{
    /** @var SchedulerService */
    private $scheduleService;

    /** @var RecurringScheduleRepository|MockObject */
    private $recurringScheduleRepository;

    public function setUp()
    {
        $this->recurringScheduleRepository = $this->createMock(RecurringScheduleRepository::class);
        $this->scheduleService = new SchedulerService($this->recurringScheduleRepository);
    }

    public function testGetAllScheduledItems()
    {
        $this->recurringScheduleRepository->expects($this->once())
            ->method('findBy')
            ->willReturn([new RecurringSchedule()]);

        $result = $this->scheduleService->getAllScheduledItems();
        $this->assertInstanceOf(RecurringSchedule::class, $result[0]);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testSave()
    {
        $this->recurringScheduleRepository->expects($this->once())->method('save');
        $this->scheduleService->saveStream(new RecurringSchedule());
        $this->addToAssertionCount(1);
    }

    public function testGetScheduleById()
    {
        $this->recurringScheduleRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(new RecurringSchedule());

        $result = $this->scheduleService->getScheduleById('id');
        $this->assertInstanceOf(RecurringSchedule::class, $result);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws RecurringScheduleNotFoundException
     */
    public function testToggleDisablingSchedule()
    {
        $recurringSchedule = new RecurringSchedule();
        $recurringSchedule->setDisabled(false);
        $this->recurringScheduleRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($recurringSchedule);
        $this->recurringScheduleRepository->expects($this->once())->method('save');

        $this->scheduleService->toggleDisablingSchedule('id');
        $this->addToAssertionCount(1);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws RecurringScheduleNotFoundException
     */
    public function testToggleScheduleWithNextExecution()
    {
        $recurringSchedule = new RecurringSchedule();
        $recurringSchedule->setDisabled(false);
        $this->recurringScheduleRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($recurringSchedule);
        $this->recurringScheduleRepository->expects($this->once())->method('save');

        $this->scheduleService->executeScheduleWithNextExecution('id');
        $this->addToAssertionCount(1);
    }

    /**
     * @throws ORMException
     * @throws RecurringScheduleNotFoundException
     */
    public function testRemoveSchedule()
    {
        $this->recurringScheduleRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(new RecurringSchedule());

        $this->scheduleService->removeSchedule('id');
        $this->addToAssertionCount(1);
    }

    /**
     * @throws ORMException
     * @throws RecurringScheduleNotFoundException
     */
    public function testUnwreckSchedule()
    {
        $this->recurringScheduleRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(new RecurringSchedule());
        $this->recurringScheduleRepository->expects($this->once())->method('save');

        $this->scheduleService->unwreckSchedule('id');
        $this->addToAssertionCount(1);
    }
}
