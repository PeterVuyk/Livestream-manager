<?php
declare(strict_types=1);

namespace App\Tests\App\Controller;

use App\Controller\ChannelController;
use App\Entity\Channel;
use App\Repository\ChannelRepository;
use App\Service\ChannelService;
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
 * @coversDefaultClass \App\Controller\ChannelController
 * @covers ::<!public>
 * @covers ::__construct
 * @uses \App\Controller\Controller
 * @uses \App\Entity\Channel
 */
class ChannelControllerTest extends TestCase
{
    /** @var FormFactoryInterface|MockObject */
    private $formFactoryMock;

    /** @var RouterInterface|MockObject */
    private $routerMock;

    /** @var FlashBagInterface|MockObject */
    private $flashBagMock;

    /** @var ChannelRepository|MockObject */
    private $channelRepositoryMock;

    /** @var TokenStorageInterface|MockObject */
    private $tokenStorageMock;

    /** @var \Twig_Environment|MockObject */
    private $twigMock;

    /** @var ChannelService|MockObject */
    private $channelServiceMock;

    /** @var ChannelController */
    private $channelController;

    public function setUp()
    {
        $this->formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $this->routerMock = $this->createMock(RouterInterface::class);
        $this->tokenStorageMock = $this->createMock(TokenStorageInterface::class);
        $this->flashBagMock = $this->createMock(FlashBagInterface::class);
        $this->twigMock = $this->createMock(\Twig_Environment::class);
        $this->channelRepositoryMock = $this->createMock(ChannelRepository::class);
        $this->channelServiceMock = $this->createMock(ChannelService::class);
        $this->channelController = new ChannelController(
            $this->twigMock,
            $this->tokenStorageMock,
            $this->formFactoryMock,
            $this->routerMock,
            $this->flashBagMock,
            $this->channelServiceMock,
            $this->channelRepositoryMock
        );
    }

    /**
     * @covers ::channelList
     */
    public function testChannelList()
    {
        $this->twigMock->expects($this->once())->method('render')->willReturn('<p>hi</p>');
        $this->channelRepositoryMock->expects($this->once())->method('findAll')->willReturn([]);

        $response = $this->channelController->channelList();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @covers ::createChannel
     */
    public function testCreateChannelOpeningPage()
    {
        $formMock = $this->createMock(FormInterface::class);
        $formMock->expects($this->once())->method('handleRequest');
        $formMock->expects($this->once())->method('createView');
        $formMock->expects($this->once())->method('isSubmitted')->willReturn(false);
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formMock);

        $this->twigMock->expects($this->once())->method('render')->willReturn('<p>hi</p>');
        $this->channelServiceMock->expects($this->never())->method('createChannel');

        $response = $this->channelController->createChannel(new Request());
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @covers ::createChannel
     */
    public function testCreateChannelSubmitForm()
    {
        $this->flashBagMock->expects($this->once())->method('add');
        $this->routerMock->expects($this->once())->method('generate')->willReturn('direction');

        $formMock = $this->createMock(FormInterface::class);
        $formMock->expects($this->once())->method('handleRequest');
        $formMock->expects($this->once())->method('getData')->willReturn(new Channel());
        $formMock->expects($this->once())->method('isSubmitted')->willReturn(true);
        $formMock->expects($this->once())->method('isValid')->willReturn(true);
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formMock);

        $this->twigMock->expects($this->never())->method('render');
        $this->channelServiceMock->expects($this->once())->method('createChannel');

        $response = $this->channelController->createChannel(new Request());
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    /**
     * @covers ::editChannel
     */
    public function testEditChannelSubmitForm()
    {
        $this->channelServiceMock->expects($this->once())->method('getChannelByName')->willReturn(new Channel());
        $this->flashBagMock->expects($this->once())->method('add');
        $this->routerMock->expects($this->once())->method('generate')->willReturn('direction');

        $formMock = $this->createMock(FormInterface::class);
        $formMock->expects($this->once())->method('handleRequest');
        $formMock->expects($this->once())->method('getData')->willReturn(new Channel());
        $formMock->expects($this->once())->method('isSubmitted')->willReturn(true);
        $formMock->expects($this->once())->method('isValid')->willReturn(true);
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formMock);

        $this->twigMock->expects($this->never())->method('render');
        $this->channelServiceMock->expects($this->once())->method('updateChannel');

        $response = $this->channelController->editChannel('channel', new Request());
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    /**
     * @covers ::editChannel
     */
    public function testEditChannelOpeningPage()
    {
        $this->channelServiceMock->expects($this->once())->method('getChannelByName')->willReturn(new Channel());

        $formMock = $this->createMock(FormInterface::class);
        $formMock->expects($this->once())->method('handleRequest');
        $formMock->expects($this->once())->method('createView');
        $formMock->expects($this->once())->method('isSubmitted')->willReturn(false);
        $this->formFactoryMock->expects($this->once())->method('create')->willReturn($formMock);

        $this->twigMock->expects($this->once())->method('render')->willReturn('<p>hi</p>');
        $this->channelServiceMock->expects($this->never())->method('updateChannel');

        $response = $this->channelController->editChannel('channel', new Request());
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }
}
