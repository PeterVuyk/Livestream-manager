<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\SetupStreamController;
use App\Entity\RecurringSchedule;
use App\Service\SchedulerService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class SetupStreamControllerTest extends TestCase
{
    /** @var SetupStreamController */
    private $setupStreamController;

    /** @var \Twig_Environment|MockObject */
    private $twigMock;

    /** @var SchedulerService|MockObject */
    private $schedulerServiceMock;

    /** @var FormFactoryInterface|MockObject */
    private $formFactoryMock;

    /** @var RouterInterface|MockObject */
    private $routerMock;

    /** @var FlashBagInterface|MockObject */
    private $flashBagMock;

    public function setUp()
    {
        $this->twigMock = $this->createMock(\Twig_Environment::class);
        $this->schedulerServiceMock = $this->createMock(SchedulerService::class);
        $this->formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $this->routerMock = $this->createMock(RouterInterface::class);
        $this->flashBagMock = $this->createMock(FlashBagInterface::class);
        $this->setupStreamController = new SetupStreamController(
            $this->schedulerServiceMock,
            $this->twigMock,
            $this->formFactoryMock,
            $this->routerMock,
            $this->flashBagMock
        );
    }

    public function testCreateStreamOpeningPage()
    {
        $formInterfaceMock = $this->createMock(FormInterface::class);
        $formInterfaceMock->expects($this->once())->method('handleRequest');
        $formInterfaceMock->expects($this->once())->method('createView');
        $formInterfaceMock->expects($this->once())->method('isSubmitted')->willReturn(false);
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formInterfaceMock);

        $this->twigMock = $this->createMock(\Twig_Environment::class);

        $result = $this->setupStreamController->createStream(new Request());
        $this->assertSame(Response::HTTP_OK, $result->getStatusCode());
    }

    public function testCreateStreamCreateWithSuccess()
    {
        $formInterfaceMock = $this->createMock(FormInterface::class);
        $formInterfaceMock->expects($this->once())->method('handleRequest');
        $formInterfaceMock->expects($this->once())->method('isSubmitted')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('isValid')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('getData')->willReturn(new RecurringSchedule());
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formInterfaceMock);

        $this->flashBagMock->expects($this->once())->method('add');

        $this->schedulerServiceMock->expects($this->once())->method('saveStream');

        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $result = $this->setupStreamController->createStream(new Request());
        $this->assertSame(Response::HTTP_FOUND, $result->getStatusCode());
    }

    public function testCreateStreamCreateFailed()
    {
        $formInterfaceMock = $this->createMock(FormInterface::class);
        $formInterfaceMock->expects($this->once())->method('handleRequest');
        $formInterfaceMock->expects($this->once())->method('isSubmitted')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('isValid')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('getData')->willReturn(new RecurringSchedule());
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formInterfaceMock);

        $this->flashBagMock->expects($this->once())->method('add');

        $this->schedulerServiceMock->expects($this->once())
            ->method('saveStream')
            ->willThrowException(new \Exception());

        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $result = $this->setupStreamController->createStream(new Request());
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
            ->willReturn(new RecurringSchedule());

        $this->flashBagMock->expects($this->never())->method('add');

        $this->twigMock = $this->createMock(\Twig_Environment::class);

        $result = $this->setupStreamController->editStream('id', new Request());
        $this->assertSame(Response::HTTP_OK, $result->getStatusCode());
    }

    public function testEditStreamEditWithSuccess()
    {
        $formInterfaceMock = $this->createMock(FormInterface::class);
        $formInterfaceMock->expects($this->once())->method('handleRequest');
        $formInterfaceMock->expects($this->once())->method('isSubmitted')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('isValid')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('getData')->willReturn(new RecurringSchedule());
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formInterfaceMock);

        $this->schedulerServiceMock->expects($this->once())
            ->method('getScheduleById')
            ->willReturn(new RecurringSchedule());
        $this->schedulerServiceMock->expects($this->once())->method('saveStream');

        $this->flashBagMock->expects($this->once())->method('add');

        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $result = $this->setupStreamController->editStream('id', new Request());
        $this->assertSame(Response::HTTP_FOUND, $result->getStatusCode());
    }

    public function testEditStreamEditFailed()
    {
        $formInterfaceMock = $this->createMock(FormInterface::class);
        $formInterfaceMock->expects($this->once())->method('handleRequest');
        $formInterfaceMock->expects($this->once())->method('isSubmitted')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('isValid')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('getData')->willReturn(new RecurringSchedule());
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formInterfaceMock);

        $this->schedulerServiceMock->expects($this->once())
            ->method('getScheduleById')
            ->willReturn(new RecurringSchedule());
        $this->schedulerServiceMock->expects($this->once())
            ->method('saveStream')
            ->willThrowException(New \Exception());

        $this->flashBagMock->expects($this->once())->method('add');

        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $result = $this->setupStreamController->editStream('id', new Request());
        $this->assertSame(Response::HTTP_FOUND, $result->getStatusCode());
    }
}
