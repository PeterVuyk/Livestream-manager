<?php
declare(strict_types=1);

namespace App\Tests\App\Controller;

use App\Controller\CameraConfigurationController;
use App\Entity\CameraConfiguration;
use App\Entity\Configuration;
use App\Service\CameraConfigurationService;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class CameraConfigurationControllerTest extends TestCase
{
    /** @var CameraConfigurationService|MockObject */
    private $cameraConfigurationServiceMock;

    /** @var FormFactoryInterface|MockObject */
    private $formFactoryMock;

    /** @var RouterInterface|MockObject */
    private $routerMock;

    /** @var FlashBagInterface|MockObject */
    private $flashBagMock;

    /** @var \Twig_Environment|MockObject */
    private $twigMock;

    /** @var CameraConfigurationController */
    private $cameraConfigurationController;

    public function setUp()
    {
        $this->cameraConfigurationServiceMock = $this->createMock(CameraConfigurationService::class);
        $this->formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $this->routerMock = $this->createMock(RouterInterface::class);
        $this->flashBagMock = $this->createMock(FlashBagInterface::class);
        $this->twigMock = $this->createMock(\Twig_Environment::class);
        $this->cameraConfigurationController = new CameraConfigurationController(
            $this->twigMock,
            $this->cameraConfigurationServiceMock,
            $this->formFactoryMock,
            $this->routerMock,
            $this->flashBagMock
        );
    }

    public function testConfigurationList()
    {
        $this->cameraConfigurationServiceMock->expects($this->once())
            ->method('getConfigurations')
            ->willReturn([new CameraConfiguration()]);

        $formInterfaceMock = $this->createMock(FormInterface::class);
        $formInterfaceMock->expects($this->once())->method('handleRequest');
        $formInterfaceMock->expects($this->once())->method('isSubmitted')->willReturn(false);
        $formInterfaceMock->expects($this->once())->method('createView');
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formInterfaceMock);

        $this->twigMock->expects($this->once())->method('render')->willReturn('<p>hi</p>');

        $result = $this->cameraConfigurationController->configurationList(new Request());
        $this->assertSame(Response::HTTP_OK, $result->getStatusCode());
    }

    public function testConfigurationListSubmitFormSuccess()
    {
        $this->cameraConfigurationServiceMock->expects($this->once())
            ->method('getConfigurations')
            ->willReturn([new CameraConfiguration()]);

        $formInterfaceMock = $this->createMock(FormInterface::class);
        $formInterfaceMock->expects($this->once())->method('handleRequest');
        $formInterfaceMock->expects($this->once())->method('isSubmitted')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('isValid')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('getData')->willReturn(new Configuration());
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formInterfaceMock);

        $this->cameraConfigurationServiceMock->expects($this->once())->method('saveConfigurations');

        $this->flashBagMock->expects($this->once())->method('add');
        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $result = $this->cameraConfigurationController->configurationList(new Request());
        $this->assertSame(Response::HTTP_FOUND, $result->getStatusCode());
    }

    public function testConfigurationListSubmitFormFailed()
    {
        $this->cameraConfigurationServiceMock->expects($this->once())
            ->method('getConfigurations')
            ->willReturn([new CameraConfiguration()]);

        $formInterfaceMock = $this->createMock(FormInterface::class);
        $formInterfaceMock->expects($this->once())->method('handleRequest');
        $formInterfaceMock->expects($this->once())->method('isSubmitted')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('isValid')->willReturn(true);
        $formInterfaceMock->expects($this->once())->method('getData')->willReturn(new Configuration());
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formInterfaceMock);

        $this->cameraConfigurationServiceMock->expects($this->once())
            ->method('saveConfigurations')
            ->willThrowException(new ORMException());

        $this->flashBagMock->expects($this->once())->method('add');
        $this->routerMock->expects($this->once())->method('generate')->willReturn('url');

        $result = $this->cameraConfigurationController->configurationList(new Request());
        $this->assertSame(Response::HTTP_FOUND, $result->getStatusCode());
    }
}
