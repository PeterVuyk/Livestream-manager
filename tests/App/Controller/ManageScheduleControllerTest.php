<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\ManageScheduleController;
use App\Entity\StreamSchedule;
use App\Exception\Repository\CouldNotModifyStreamScheduleException;
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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @coversDefaultClass \App\Controller\ManageScheduleController
 * @covers ::<!public>
 * @covers ::__construct
 * @uses \App\Controller\Controller
 * @uses \App\Service\ManageScheduleService
 * @uses \App\Entity\StreamSchedule
 */
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

    /** @var TokenStorageInterface|MockObject */
    private $tokenStorageMock;

    /** @var \Twig_Environment|MockObject */
    private $twigMock;

    public function setUp()
    {
        $this->manageScheduleService = $this->createMock(ManageScheduleService::class);
        $this->routerMock = $this->createMock(RouterInterface::class);
        $this->flashBagMock = $this->createMock(FlashBagInterface::class);
        $this->formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $this->twigMock = $this->createMock(\Twig_Environment::class);
        $this->tokenStorageMock = $this->createMock(TokenStorageInterface::class);
        $this->manageScheduleController = new ManageScheduleController(
            $this->twigMock,
            $this->tokenStorageMock,
            $this->manageScheduleService,
            $this->routerMock,
            $this->flashBagMock,
            $this->formFactoryMock
        );
    }

    /**
     * @covers ::toggleDisablingSchedule
     */
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

    /**
     * @covers ::toggleDisablingSchedule
     */
    public function testToggleDisablingScheduleFailed()
    {
        $this->routerMock->expects($this->once())->method('generate')->willReturn('<url>hi</url>');
        $this->manageScheduleService->expects($this->once())
            ->method('getScheduleById')->willReturn(new StreamSchedule());
        $this->manageScheduleService->expects($this->once())
            ->method('toggleDisablingSchedule')
            ->willThrowException(CouldNotModifyStreamScheduleException::forError(new ORMException()));

        $this->flashBagMock->expects($this->once())->method('add');

        $response = $this->manageScheduleController->toggleDisablingSchedule('id');
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    /**
     * @covers ::removeSchedule
     */
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

    /**
     * @covers ::removeSchedule
     */
    public function testRemoveScheduleFailed()
    {
        $this->routerMock->expects($this->once())->method('generate')->willReturn('<url>hi</url>');
        $this->flashBagMock->expects($this->once())->method('add');
        $this->manageScheduleService->expects($this->once())
            ->method('getScheduleById')->willReturn(new StreamSchedule());
        $this->manageScheduleService->expects($this->once())
            ->method('removeSchedule')
            ->willThrowException(CouldNotModifyStreamScheduleException::forError(new ORMException()));

        $response = $this->manageScheduleController->removeSchedule('id');
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    /**
     * @covers ::unwreckSchedule
     */
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

    /**
     * @covers ::unwreckSchedule
     */
    public function testUnwreckScheduleFailed()
    {
        $this->routerMock->expects($this->once())->method('generate')->willReturn('<url>hi</url>');
        $this->flashBagMock->expects($this->once())->method('add');

        $this->manageScheduleService->expects($this->once())
            ->method('getScheduleById')->willReturn(new StreamSchedule());
        $this->manageScheduleService->expects($this->once())
            ->method('unwreckSchedule')
            ->willThrowException(CouldNotModifyStreamScheduleException::forError(new ORMException()));

        $response = $this->manageScheduleController->unwreckSchedule('id');
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    /**
     * @covers ::editSchedule
     */
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

    /**
     * @covers ::editSchedule
     */
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

    /**
     * @covers ::editSchedule
     */
    public function testEditScheduleCouldNotGetScheduleById()
    {
        $this->manageScheduleService->expects($this->once())->method('getScheduleById')->willReturn(null);
        $this->flashBagMock->expects($this->once())->method('add');
        $this->routerMock->expects($this->once())->method('generate')->willReturn('link');
        $this->formFactoryMock->expects($this->never())->method('create');

        $response = $this->manageScheduleController->editSchedule('scheduleId', new Request());
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    /**
     * @covers ::editSchedule
     */
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
            ->willThrowException(CouldNotModifyStreamScheduleException::forError(New ORMException()));

        $this->flashBagMock->expects($this->once())->method('add');

        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $response = $this->manageScheduleController->editSchedule('id', new Request());
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }
}
