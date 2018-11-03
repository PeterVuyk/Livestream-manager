<?php
declare(strict_types=1);

namespace App\Tests\App\Controller;

use App\Controller\RecurringSchedulerController;
use App\Entity\StreamSchedule;
use App\Service\SchedulerService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;

class RecurringSchedulerControllerTest extends TestCase
{
    /** @var RecurringSchedulerController */
    private $recurringSchedulerController;

    /** @var \Twig_Environment|MockObject */
    private $twigMock;

    /** @var SchedulerService|MockObject */
    private $schedulerServiceMock;

    /** @var FormFactoryInterface|MockObject */
    private $formFactoryMock;

    /** @var RouterInterface|MockObject */
    private $routerMock;

    public function setUp()
    {
        $this->twigMock = $this->createMock(\Twig_Environment::class);
        $this->schedulerServiceMock = $this->createMock(SchedulerService::class);
        $this->formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $this->routerMock= $this->createMock(RouterInterface::class);
        $this->recurringSchedulerController = new RecurringSchedulerController(
            $this->schedulerServiceMock,
            $this->twigMock,
            $this->formFactoryMock,
            $this->routerMock
        );
    }

    public function testList()
    {
        $this->twigMock->expects($this->once())->method('render')->willReturn('<p>hi</p>');
        $result = $this->recurringSchedulerController->list();
        $this->assertSame(Response::HTTP_OK, $result->getStatusCode());
    }

    public function testCreateStreamOpeningPage()
    {
        $formInterfaceMock = $this->createMock(FormInterface::class);
        $formInterfaceMock->expects($this->once())->method('handleRequest');
        $formInterfaceMock->expects($this->once())->method('createView');
        $formInterfaceMock->expects($this->once())->method('isSubmitted')->willReturn(false);
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formInterfaceMock);

        $this->twigMock = $this->createMock(\Twig_Environment::class);

        $result = $this->recurringSchedulerController->createStream(new Request());
        $this->assertSame(Response::HTTP_OK, $result->getStatusCode());
    }

    public function testCreateStreamCreateWithSuccess()
    {
        $formInterfaceMock = $this->createMock(FormInterface::class);
        $formInterfaceMock->expects($this->once())->method('handleRequest');
        $formInterfaceMock->expects($this->once())->method('isSubmitted')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('isValid')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('getData')->willReturn(new StreamSchedule());
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formInterfaceMock);

        $flashBagMock = $this->createMock(FlashBagInterface::class);
        $flashBagMock->expects($this->once())->method('add');
        $sessionMock = $this->createMock(Session::class);
        $sessionMock->method('getFlashBag')->willReturn($flashBagMock);
        ($request = new Request())->setSession($sessionMock);

        $this->schedulerServiceMock->expects($this->once())->method('saveStream');

        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $result = $this->recurringSchedulerController->createStream($request);
        $this->assertSame(Response::HTTP_FOUND, $result->getStatusCode());
    }

    public function testCreateStreamCreateFailed()
    {
        $formInterfaceMock = $this->createMock(FormInterface::class);
        $formInterfaceMock->expects($this->once())->method('handleRequest');
        $formInterfaceMock->expects($this->once())->method('isSubmitted')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('isValid')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('getData')->willReturn(new StreamSchedule());
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formInterfaceMock);

        $flashBagMock = $this->createMock(FlashBagInterface::class);
        $flashBagMock->expects($this->once())->method('add');
        $sessionMock = $this->createMock(Session::class);
        $sessionMock->method('getFlashBag')->willReturn($flashBagMock);
        ($request = new Request())->setSession($sessionMock);

        $this->schedulerServiceMock->expects($this->once())
            ->method('saveStream')
            ->willThrowException(new \Exception());

        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $result = $this->recurringSchedulerController->createStream($request);
        $this->assertSame(Response::HTTP_FOUND, $result->getStatusCode());
    }

    public function testEditStreamOpeningPage()
    {
        $formInterfaceMock = $this->createMock(FormInterface::class);
        $formInterfaceMock->expects($this->once())->method('handleRequest');
        $formInterfaceMock->expects($this->once())->method('createView');
        $formInterfaceMock->expects($this->once())->method('isSubmitted')->willReturn(false);
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formInterfaceMock);

        $this->schedulerServiceMock->expects($this->once())
            ->method('getScheduleById')
            ->willReturn(new StreamSchedule());

        $sessionMock = $this->createMock(Session::class);
        $sessionMock->expects($this->never())->method('getFlashBag');
        ($request = new Request())->setSession($sessionMock);

        $this->twigMock = $this->createMock(\Twig_Environment::class);

        $result = $this->recurringSchedulerController->editStream('id', $request);
        $this->assertSame(Response::HTTP_OK, $result->getStatusCode());
    }

    public function testEditStreamEditWithSuccess()
    {
        $formInterfaceMock = $this->createMock(FormInterface::class);
        $formInterfaceMock->expects($this->once())->method('handleRequest');
        $formInterfaceMock->expects($this->once())->method('isSubmitted')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('isValid')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('getData')->willReturn(new StreamSchedule());
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formInterfaceMock);

        $this->schedulerServiceMock->expects($this->once())
            ->method('getScheduleById')
            ->willReturn(new StreamSchedule());
        $this->schedulerServiceMock->expects($this->once())->method('saveStream');

        $flashBagMock = $this->createMock(FlashBagInterface::class);
        $flashBagMock->expects($this->once())->method('add');
        $sessionMock = $this->createMock(Session::class);
        $sessionMock->method('getFlashBag')->willReturn($flashBagMock);
        ($request = new Request())->setSession($sessionMock);

        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $result = $this->recurringSchedulerController->editStream('id', $request);
        $this->assertSame(Response::HTTP_FOUND, $result->getStatusCode());
    }

    public function testEditStreamEditFailed()
    {
        $formInterfaceMock = $this->createMock(FormInterface::class);
        $formInterfaceMock->expects($this->once())->method('handleRequest');
        $formInterfaceMock->expects($this->once())->method('isSubmitted')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('isValid')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('getData')->willReturn(new StreamSchedule());
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formInterfaceMock);

        $this->schedulerServiceMock->expects($this->once())
            ->method('getScheduleById')
            ->willReturn(new StreamSchedule());
        $this->schedulerServiceMock->expects($this->once())
            ->method('saveStream')
            ->willThrowException(New \Exception());

        $flashBagMock = $this->createMock(FlashBagInterface::class);
        $flashBagMock->expects($this->once())->method('add');
        $sessionMock = $this->createMock(Session::class);
        $sessionMock->method('getFlashBag')->willReturn($flashBagMock);
        ($request = new Request())->setSession($sessionMock);

        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $result = $this->recurringSchedulerController->editStream('id', $request);
        $this->assertSame(Response::HTTP_FOUND, $result->getStatusCode());
    }
}
