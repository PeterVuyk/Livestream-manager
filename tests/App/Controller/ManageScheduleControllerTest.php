<?php
declare(strict_types=1);

namespace App\Tests\App\Controller;

use App\Controller\ManageScheduleController;
use App\Entity\StreamSchedule;
use App\Service\ManageScheduleService;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class ManageScheduleControllerTest extends TestCase
{
    /** @var ManageScheduleController */
    private $manageScheduleController;

    /** @var ManageScheduleService|MockObject */
    private $manageScheduleService;

    /** @var RouterInterface|MockObject */
    private $routerMock;

    /** @var FlashBagInterface|MockObject */
    private $flashBagMock;

    /** @var FormFactoryInterface|MockObject */
    private $formFactoryMock;

    /** @var \Twig_Environment|MockObject */
    private $twigMock;

    public function setUp()
    {
        $this->manageScheduleService = $this->createMock(ManageScheduleService::class);
        $this->routerMock = $this->createMock(RouterInterface::class);
        $this->flashBagMock = $this->createMock(FlashBagInterface::class);
        $this->formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $this->twigMock = $this->createMock(\Twig_Environment::class);
        $this->manageScheduleController = new ManageScheduleController(
            $this->twigMock,
            $this->manageScheduleService,
            $this->routerMock,
            $this->flashBagMock,
            $this->formFactoryMock
        );
    }

    public function testToggleDisablingScheduleSuccess()
    {
        $this->routerMock->expects($this->once())->method('generate')->willReturn('<url>hi</url>');
        $this->manageScheduleService->expects($this->once())
            ->method('getScheduleById')->willReturn(new StreamSchedule());
        $this->manageScheduleService->expects($this->once())
            ->method('toggleDisablingSchedule');

        $response = $this->manageScheduleController->toggleDisablingSchedule('id');
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    public function testToggleDisablingScheduleFailed()
    {
        $this->routerMock->expects($this->once())->method('generate')->willReturn('<url>hi</url>');
        $this->manageScheduleService->expects($this->once())
            ->method('getScheduleById')->willReturn(new StreamSchedule());
        $this->manageScheduleService->expects($this->once())
            ->method('toggleDisablingSchedule')->willThrowException(new ORMException());

        $this->flashBagMock->expects($this->once())->method('add');

        $response = $this->manageScheduleController->toggleDisablingSchedule('id');
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    public function testRemoveScheduleSuccess()
    {
        $this->routerMock->expects($this->once())->method('generate')->willReturn('<url>hi</url>');
        $this->manageScheduleService->expects($this->once())
            ->method('getScheduleById')->willReturn(new StreamSchedule());
        $this->manageScheduleService->expects($this->once())
            ->method('removeSchedule');

        $response = $this->manageScheduleController->removeSchedule('id');
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    public function testRemoveScheduleFailed()
    {
        $this->routerMock->expects($this->once())->method('generate')->willReturn('<url>hi</url>');
        $this->flashBagMock->expects($this->once())->method('add');
        $this->manageScheduleService->expects($this->once())
            ->method('getScheduleById')->willReturn(new StreamSchedule());
        $this->manageScheduleService->expects($this->once())
            ->method('removeSchedule')->willThrowException(new ORMException());

        $response = $this->manageScheduleController->removeSchedule('id');
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    public function testUnwreckScheduleSuccess()
    {
        $this->routerMock->expects($this->once())->method('generate')->willReturn('<url>hi</url>');
        $this->manageScheduleService->expects($this->once())
            ->method('getScheduleById')->willReturn(new StreamSchedule());
        $this->manageScheduleService->expects($this->once())
            ->method('unwreckSchedule');

        $response = $this->manageScheduleController->unwreckSchedule('id');
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    public function testUnwreckScheduleFailed()
    {
        $this->routerMock->expects($this->once())->method('generate')->willReturn('<url>hi</url>');
        $this->flashBagMock->expects($this->once())->method('add');

        $this->manageScheduleService->expects($this->once())
            ->method('getScheduleById')->willReturn(new StreamSchedule());
        $this->manageScheduleService->expects($this->once())
            ->method('unwreckSchedule')->willThrowException(new ORMException());

        $response = $this->manageScheduleController->unwreckSchedule('id');
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    public function testEditStreamOpeningPage()
    {
        $formInterfaceMock = $this->createMock(FormInterface::class);
        $formInterfaceMock->expects($this->once())->method('handleRequest');
        $formInterfaceMock->expects($this->once())->method('createView');
        $formInterfaceMock->expects($this->once())->method('isSubmitted')->willReturn(false);
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formInterfaceMock);

        $this->manageScheduleService->expects($this->once())
            ->method('getScheduleById')
            ->willReturn(new StreamSchedule());

        $this->flashBagMock->expects($this->never())->method('add');

        $this->twigMock = $this->createMock(\Twig_Environment::class);

        $response = $this->manageScheduleController->editSchedule('id', new Request());
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testEditStreamEditWithSuccess()
    {
        $formInterfaceMock = $this->createMock(FormInterface::class);
        $formInterfaceMock->expects($this->once())->method('handleRequest');
        $formInterfaceMock->expects($this->once())->method('isSubmitted')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('isValid')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('getData')->willReturn(new StreamSchedule());
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formInterfaceMock);

        $this->manageScheduleService->expects($this->once())
            ->method('getScheduleById')
            ->willReturn(new StreamSchedule());
        $this->manageScheduleService->expects($this->once())->method('saveSchedule');

        $this->flashBagMock->expects($this->once())->method('add');

        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $response = $this->manageScheduleController->editSchedule('id', new Request());
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    public function testEditScheduleCouldNotGetScheduleById()
    {
        $this->manageScheduleService->expects($this->once())->method('getScheduleById')->willReturn(null);
        $this->flashBagMock->expects($this->once())->method('add');
        $this->routerMock->expects($this->once())->method('generate')->willReturn('link');
        $this->formFactoryMock->expects($this->never())->method('create');

        $response = $this->manageScheduleController->editSchedule('scheduleId', new Request());
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    public function testEditStreamEditFailed()
    {
        $formInterfaceMock = $this->createMock(FormInterface::class);
        $formInterfaceMock->expects($this->once())->method('handleRequest');
        $formInterfaceMock->expects($this->once())->method('isSubmitted')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('isValid')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('getData')->willReturn(new StreamSchedule());
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formInterfaceMock);

        $this->manageScheduleService->expects($this->once())
            ->method('getScheduleById')
            ->willReturn(new StreamSchedule());
        $this->manageScheduleService->expects($this->once())
            ->method('saveSchedule')
            ->willThrowException(New \Exception());

        $this->flashBagMock->expects($this->once())->method('add');

        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $response = $this->manageScheduleController->editSchedule('id', new Request());
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }
}
