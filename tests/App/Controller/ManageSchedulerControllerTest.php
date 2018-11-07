<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\ManageSchedulerController;
use App\Exception\RecurringScheduleNotFoundException;
use App\Service\SchedulerService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class ManageSchedulerControllerTest extends TestCase
{
    /** @var ManageSchedulerController */
    private $manageSchedulerController;

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
        $this->manageSchedulerController = new ManageSchedulerController(
            $this->twigMock,
            $this->schedulerServiceMock,
            $this->routerMock,
            $this->flashBagMock
        );
    }

    public function testList()
    {
        $this->twigMock->expects($this->once())->method('render')->willReturn('<p>hi</p>');
        $result = $this->manageSchedulerController->list();
        $this->assertSame(Response::HTTP_OK, $result->getStatusCode());
    }

    public function testToggleDisablingScheduleSuccess()
    {
        $this->schedulerServiceMock->expects($this->once())->method('toggleDisablingSchedule');
        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $result = $this->manageSchedulerController->toggleDisablingSchedule('id');
        $this->assertSame(Response::HTTP_FOUND, $result->getStatusCode());
    }

    public function testToggleDisablingScheduleFailed()
    {
        $this->schedulerServiceMock->expects($this->once())
            ->method('toggleDisablingSchedule')
            ->willThrowException(new RecurringScheduleNotFoundException('id'));

        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $this->flashBagMock->expects($this->once())->method('add');

        $result = $this->manageSchedulerController->toggleDisablingSchedule('id');
        $this->assertSame(Response::HTTP_FOUND, $result->getStatusCode());
    }

    public function testExecuteScheduleWithNextExecutionSuccess()
    {
        $this->schedulerServiceMock->expects($this->once())->method('executeScheduleWithNextExecution');
        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $result = $this->manageSchedulerController->executeScheduleWithNextExecution('id');
        $this->assertSame(Response::HTTP_FOUND, $result->getStatusCode());
    }

    public function testExecuteScheduleWithNextExecutionFailed()
    {
        $this->schedulerServiceMock->expects($this->once())
            ->method('executeScheduleWithNextExecution')
            ->willThrowException(new RecurringScheduleNotFoundException('id'));

        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $this->flashBagMock->expects($this->once())->method('add');

        $result = $this->manageSchedulerController->executeScheduleWithNextExecution('id');
        $this->assertSame(Response::HTTP_FOUND, $result->getStatusCode());
    }

    public function testRemoveScheduleSuccess()
    {
        $this->schedulerServiceMock->expects($this->once())->method('removeSchedule');
        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $result = $this->manageSchedulerController->removeSchedule('id');
        $this->assertSame(Response::HTTP_FOUND, $result->getStatusCode());
    }

    public function testRemoveScheduleFailed()
    {
        $this->schedulerServiceMock->expects($this->once())
            ->method('removeSchedule')
            ->willThrowException(new RecurringScheduleNotFoundException('id'));
        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $this->flashBagMock->expects($this->once())->method('add');

        $result = $this->manageSchedulerController->removeSchedule('id');
        $this->assertSame(Response::HTTP_FOUND, $result->getStatusCode());
    }

    public function testUnwreckScheduleSuccess()
    {
        $this->schedulerServiceMock->expects($this->once())->method('unwreckSchedule');
        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $this->flashBagMock->expects($this->never())->method('add');

        $result = $this->manageSchedulerController->unwreckSchedule('id');
        $this->assertSame(Response::HTTP_FOUND, $result->getStatusCode());
    }

    public function testUnwreckScheduleFailed()
    {
        $this->schedulerServiceMock->expects($this->once())
            ->method('unwreckSchedule')
            ->willThrowException(new RecurringScheduleNotFoundException('id'));
        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $this->flashBagMock->expects($this->once())->method('add');

        $result = $this->manageSchedulerController->unwreckSchedule('id');
        $this->assertSame(Response::HTTP_FOUND, $result->getStatusCode());
    }
}
