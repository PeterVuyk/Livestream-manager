<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\ScheduleController;
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

/**
 * @coversDefaultClass \App\Controller\ScheduleController
 * @covers ::<!public>
 * @covers ::__construct
 * @uses \App\Controller\Controller
 * @uses \App\Service\ManageScheduleService
 * @uses \App\Entity\StreamSchedule
 */
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

    /**
     * @covers ::list
     */
    public function testList()
    {
        $this->manageScheduleService->expects($this->once())
            ->method('getOnetimeSchedules')->willReturn([new StreamSchedule()]);
        $this->manageScheduleService->expects($this->once())
            ->method('getRecurringSchedules')->willReturn([new StreamSchedule()]);
        $this->twigMock->expects($this->once())->method('render')->willReturn('<p>hi</p>');
        $response = $this->scheduleController->list();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @covers ::createRecurringSchedule
     */
    public function testCreateRecurringScheduleSuccess()
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

        $response = $this->scheduleController->createRecurringSchedule(new Request());
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    /**
     * @covers ::createRecurringSchedule
     */
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

        $response = $this->scheduleController->createRecurringSchedule(new Request());
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    /**
     * @covers ::createRecurringSchedule
     */
    public function testScheduleOpeningPage()
    {
        $formInterfaceMock = $this->createMock(FormInterface::class);
        $formInterfaceMock->expects($this->once())->method('handleRequest');
        $formInterfaceMock->expects($this->once())->method('createView');
        $formInterfaceMock->expects($this->once())->method('isSubmitted')->willReturn(false);
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formInterfaceMock);

        $this->twigMock = $this->createMock(\Twig_Environment::class);

        $response = $this->scheduleController->createRecurringSchedule(new Request());
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @covers ::createOnetimeSchedule
     */
    public function testCreateOnetimeScheduleOpeningPage()
    {
        $formInterface = $this->createMock(FormInterface::class);
        $formInterface->expects($this->once())->method('handleRequest');
        $formInterface->expects($this->once())->method('createView');
        $formInterface->expects($this->once())->method('isSubmitted')->willReturn(false);
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formInterface);

        $this->twigMock->expects($this->once())->method('render')->willReturn('<p>hi</p>');

        $response = $this->scheduleController->createOnetimeSchedule(new Request());
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @covers ::createOnetimeSchedule
     */
    public function testCreateOnetimeScheduleSubmittingFormSuccess()
    {
        $formInterface = $this->createMock(FormInterface::class);
        $formInterface->expects($this->once())->method('handleRequest');
        $formInterface->expects($this->once())->method('isSubmitted')->willReturn(true);
        $formInterface->expects($this->once())->method('isValid')->willReturn(true);
        $formInterface->expects($this->once())->method('getData')->willReturn(new StreamSchedule());
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formInterface);

        $this->manageScheduleService->expects($this->once())->method('saveSchedule');
        $this->flashBagMock->expects($this->once())->method('add');

        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $response = $this->scheduleController->createOnetimeSchedule(new Request());
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    /**
     * @covers ::createOnetimeSchedule
     */
    public function testCreateOnetimeScheduleSubmittingFormFailed()
    {
        $formInterface = $this->createMock(FormInterface::class);
        $formInterface->expects($this->once())->method('handleRequest');
        $formInterface->expects($this->once())->method('isSubmitted')->willReturn(true);
        $formInterface->expects($this->once())->method('isValid')->willReturn(true);
        $formInterface->expects($this->once())->method('getData')->willReturn(new StreamSchedule());
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formInterface);

        $this->manageScheduleService->expects($this->once())->method('saveSchedule')->willThrowException(new ORMException());
        $this->flashBagMock->expects($this->once())->method('add');

        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $response = $this->scheduleController->createOnetimeSchedule(new Request());
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }
}
