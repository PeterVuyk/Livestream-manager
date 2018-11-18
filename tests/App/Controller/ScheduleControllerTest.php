<?php
declare(strict_types=1);

namespace App\Tests\App\Controller;

use App\Controller\ScheduleController;
use App\Entity\StreamSchedule;
use App\Service\ManageScheduleService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class ScheduleControllerTest extends TestCase
{
    /** @var ScheduleController */
    private $scheduleController;

    /** @var \Twig_Environment|MockObject */
    private $twigMock;

    /** @var ManageScheduleService|MockObject */
    private $manageScheduleService;

    /** @var FormFactoryInterface|MockObject */
    private $formFactoryMock;

    /** @var RouterInterface|MockObject */
    private $routerMock;

    /** @var FlashBagInterface|MockObject */
    private $flashBagMock;

    public function setUp()
    {
        $this->twigMock = $this->createMock(\Twig_Environment::class);
        $this->manageScheduleService = $this->createMock(ManageScheduleService::class);
        $this->formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $this->routerMock = $this->createMock(RouterInterface::class);
        $this->flashBagMock = $this->createMock(FlashBagInterface::class);
        $this->scheduleController = new ScheduleController(
            $this->manageScheduleService,
            $this->twigMock,
            $this->formFactoryMock,
            $this->routerMock,
            $this->flashBagMock
        );
    }

    public function testList()
    {
        $this->manageScheduleService->expects($this->once())
            ->method('getOnetimeSchedules')->willReturn([new StreamSchedule()]);
        $this->manageScheduleService->expects($this->once())
            ->method('getRecurringSchedules')->willReturn([new StreamSchedule()]);
        $this->twigMock->expects($this->once())->method('render')->willReturn('<p>hi</p>');
        $result = $this->scheduleController->list();
        $this->assertSame(Response::HTTP_OK, $result->getStatusCode());
    }

    public function testCreateScheduleSuccess()
    {
        $formInterfaceMock = $this->createMock(FormInterface::class);
        $formInterfaceMock->expects($this->once())->method('handleRequest');
        $formInterfaceMock->expects($this->once())->method('isSubmitted')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('isValid')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('getData')->willReturn(new StreamSchedule());
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formInterfaceMock);

        $this->flashBagMock->expects($this->once())->method('add');

        $this->manageScheduleService->expects($this->once())->method('saveSchedule');

        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $result = $this->scheduleController->createRecurringSchedule(new Request());
        $this->assertSame(Response::HTTP_FOUND, $result->getStatusCode());
    }

    public function testCreateScheduleFailed()
    {
        $formInterfaceMock = $this->createMock(FormInterface::class);
        $formInterfaceMock->expects($this->once())->method('handleRequest');
        $formInterfaceMock->expects($this->once())->method('isSubmitted')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('isValid')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('getData')->willReturn(new StreamSchedule());
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formInterfaceMock);

        $this->flashBagMock->expects($this->once())->method('add');

        $this->manageScheduleService->expects($this->once())
            ->method('saveSchedule')
            ->willThrowException(new \Exception());

        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $result = $this->scheduleController->createRecurringSchedule(new Request());
        $this->assertSame(Response::HTTP_FOUND, $result->getStatusCode());
    }

    public function testScheduleOpeningPage()
    {
        $formInterfaceMock = $this->createMock(FormInterface::class);
        $formInterfaceMock->expects($this->once())->method('handleRequest');
        $formInterfaceMock->expects($this->once())->method('createView');
        $formInterfaceMock->expects($this->once())->method('isSubmitted')->willReturn(false);
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formInterfaceMock);

        $this->twigMock = $this->createMock(\Twig_Environment::class);

        $result = $this->scheduleController->createRecurringSchedule(new Request());
        $this->assertSame(Response::HTTP_OK, $result->getStatusCode());
    }
}
