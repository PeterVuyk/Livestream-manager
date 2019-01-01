<?php
declare(strict_types=1);

namespace App\Tests\App\Controller\Api;

use App\Controller\Api\ApiSchedulerController;
use App\Entity\ScheduleLog;
use App\Entity\StreamSchedule;
use App\Service\ManageScheduleService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @coversDefaultClass \App\Controller\Api\ApiSchedulerController
 * @covers ::<!public>
 * @covers ::__construct
 * @uses \App\Entity\ScheduleLog
 * @uses \App\Entity\StreamSchedule
 * @uses \App\Entity\Weekday
 */
class ApiSchedulerControllerTest extends TestCase
{
    /** @var ManageScheduleService|MockObject */
    private $manageScheduleServiceMock;

    /** @var ApiSchedulerController */
    private $schedulerController;

    public function setUp()
    {
        $this->manageScheduleServiceMock = $this->createMock(ManageScheduleService::class);
        $this->schedulerController = new ApiSchedulerController($this->manageScheduleServiceMock);
    }

    /**
     * @covers ::getStreamSchedule
     */
    public function testGetStreamSchedule()
    {
        $this->manageScheduleServiceMock->expects($this->once())
            ->method('getAllSchedules')
            ->willReturn([$this->getStreamSchedule()]);

        $response = $this->schedulerController->getStreamSchedule();
        $this->assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    private function getStreamSchedule()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setId('f1f28b0c-9ec1-47ab-86e6-af24a50293c1');
        $streamSchedule->setName('some-name');
        $streamSchedule->setLastExecution(new \DateTime());
        $streamSchedule->setDisabled(false);
        $streamSchedule->setWrecked(false);
        $streamSchedule->setExecutionTime(new \DateTime());
        $streamSchedule->setIsRunning(true);
        $streamSchedule->setOnetimeExecutionDate(new \DateTime());
        $streamSchedule->setExecutionDay(1);
        $scheduleLog = new ScheduleLog(new StreamSchedule(), true, 'message');
        $streamSchedule->addScheduleLog($scheduleLog);

        return $streamSchedule;
    }

}
