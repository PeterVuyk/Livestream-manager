<?php
declare(strict_types=1);

namespace App\Tests\EventSubscriber;

use App\EventSubscriber\LocaleSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @coversDefaultClass \App\EventSubscriber\LocaleSubscriber
 * @covers ::<!public>
 * @covers ::__construct()
 */
class LocaleSubscriberTest extends TestCase
{
    /** @var LocaleSubscriber */
    private $localeSubscriber;

    public function setUp()
    {
        $this->localeSubscriber = new LocaleSubscriber('en');
    }

    /**
     * @covers ::onKernelRequest
     */
    public function testOnKernelRequestHasNoPreviousSession()
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())->method('hasPreviousSession')->willReturn(false);
        $eventMock = $this->createMock(GetResponseEvent::class);
        $eventMock->expects($this->once())->method('getRequest')->willReturn($requestMock);

        $this->localeSubscriber->onKernelRequest($eventMock);
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::onKernelRequest
     */
    public function testOnKernelRequestHasPreviousSessionSetLocale()
    {
        $sessionMock = $this->createMock(SessionInterface::class);
        $sessionMock->expects($this->atLeastOnce())->method('set');
        $request = new Request();
        $parameterBagMock = $this->createMock(ParameterBag::class);
        $parameterBagMock->expects($this->once())->method('has')->willReturn(true);
        $request->cookies = $parameterBagMock;
        $request->attributes->set(LocaleSubscriber::KEY_LOCALE, 'nl');
        $request->setSession($sessionMock);
        $eventMock = $this->createMock(GetResponseEvent::class);
        $eventMock->expects($this->once())->method('getRequest')->willReturn($request);

        $this->localeSubscriber->onKernelRequest($eventMock);
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::onKernelRequest
     */
    public function testOnKernelRequestHasPreviousSessionSetLocaleFromSession()
    {
        $sessionMock = $this->createMock(SessionInterface::class);
        $sessionMock->expects($this->atLeastOnce())->method('get')->willReturn('nl');
        $request = new Request();
        $parameterBagMock = $this->createMock(ParameterBag::class);
        $parameterBagMock->expects($this->once())->method('has')->willReturn(true);
        $request->cookies = $parameterBagMock;
        $request->setSession($sessionMock);
        $eventMock = $this->createMock(GetResponseEvent::class);
        $eventMock->expects($this->once())->method('getRequest')->willReturn($request);

        $this->localeSubscriber->onKernelRequest($eventMock);
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertArrayHasKey(KernelEvents::REQUEST, LocaleSubscriber::getSubscribedEvents());
    }
}
