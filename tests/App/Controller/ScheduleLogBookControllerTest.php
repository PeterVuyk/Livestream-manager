<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\ScheduleLogBookController;
use App\Entity\RecurringSchedule;
use App\Service\SchedulerService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class ScheduleLogBookControllerTest extends TestCase
{
    /** @var ScheduleLogBookController */
    private $streamLoggingController;

    /** @var \Twig_Environment|MockObject */
    private $twigMock;

    /** @var SchedulerService|MockObject */
    private $schedulerServiceMock;

    /** @var RouterInterface|MockObject */
    private $routerMock;

    /** @var FlashBagInterface|MockObject */
    private $flashBagMock;

    public function setUp()
    {
        $this->twigMock = $this->createMock(\Twig_Environment::class);
        $this->schedulerServiceMock = $this->createMock(SchedulerService::class);
        $this->routerMock = $this->createMock(RouterInterface::class);
        $this->flashBagMock = $this->createMock(FlashBagInterface::class);
        $this->streamLoggingController = new ScheduleLogBookController(
            $this->twigMock,
            $this->schedulerServiceMock,
            $this->routerMock,
            $this->flashBagMock
        );
    }

    public function testViewLoggingSuccess()
    {
        $this->schedulerServiceMock->expects($this->once())
            ->method('getScheduleById')
            ->willReturn(new RecurringSchedule());

        $result = $this->streamLoggingController->viewLogging('id');
        $this->assertSame(Response::HTTP_OK, $result->getStatusCode());
    }

    public function testViewLoggingFailed()
    {
        $this->schedulerServiceMock->expects($this->once())
            ->method('getScheduleById')
            ->willThrowException(new \Exception());
        $this->flashBagMock->expects($this->once())->method('add');
        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $result = $this->streamLoggingController->viewLogging('id');
        $this->assertSame(Response::HTTP_FOUND, $result->getStatusCode());
    }
}
