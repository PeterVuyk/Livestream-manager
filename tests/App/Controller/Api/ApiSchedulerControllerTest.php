<?php
declare(strict_types=1);

namespace App\Tests\Controller\Api;

use App\Controller\Api\ApiSchedulerController;
use App\Entity\StreamSchedule;
use App\Service\ManageScheduleService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @coversDefaultClass \App\Controller\Api\ApiSchedulerController
 * @covers ::<!public>
 * @covers ::__construct
 * @uses \App\Entity\ScheduleLog
 * @uses \App\Entity\StreamSchedule
 * @uses \App\Entity\Api\StreamScheduleDTO
 * @uses \App\Entity\Weekday
 */
class ApiSchedulerControllerTest extends TestCase
{
    /** @var ManageScheduleService|MockObject */
    private $manageScheduleServiceMock;

    /** @var LoggerInterface|MockObject */
    private $loggerMock;

    /** @var ApiSchedulerController */
    private $schedulerController;

    public function setUp()
    {
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->manageScheduleServiceMock = $this->createMock(ManageScheduleService::class);
        $this->schedulerController = new ApiSchedulerController($this->loggerMock, $this->manageScheduleServiceMock);
    }

    /**
     * @covers ::getStreamSchedule
     */
    public function testGetStreamScheduleSuccess()
    {
        $streamSchedule = $this->getStreamSchedule();
        $streamSchedule->setWrecked(true);
        $this->manageScheduleServiceMock->expects($this->once())
            ->method('getAllSchedules')
            ->willReturn([$this->getStreamSchedule(), $streamSchedule]);
        $this->loggerMock->expects($this->never())->method('error');

        $response = $this->schedulerController->getStreamSchedule();
        $this->assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertSame(1, count(json_decode($response->getContent(), true)));
    }

    /**
     * @covers ::getStreamSchedule
     */
    public function testGetStreamScheduleFailed()
    {
        $this->manageScheduleServiceMock->expects($this->once())
            ->method('getAllSchedules')
            ->willReturn([new StreamSchedule()]);
        $this->loggerMock->expects($this->once())->method('error');

        $response = $this->schedulerController->getStreamSchedule();
        $this->assertSame(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
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
        $streamSchedule->setStreamDuration(4);
        $streamSchedule->setExecutionDay(1);
        return $streamSchedule;
    }
}
