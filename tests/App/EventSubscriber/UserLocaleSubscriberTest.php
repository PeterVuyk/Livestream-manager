<?php
declare(strict_types=1);

namespace App\Tests\EventSubscriber;

use App\Entity\User;
use App\EventSubscriber\UserLocaleSubscriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * @coversDefaultClass \App\EventSubscriber\UserLocaleSubscriber
 * @covers ::<!public>
 * @covers ::__construct()
 * @uses \App\Entity\User
 */
class UserLocaleSubscriberTest extends TestCase
{
    /** @var SessionInterface|MockObject */
    private $sessionMock;

    /** @var UserLocaleSubscriber */
    private $userLocaleSubscriber;

    public function setUp()
    {
        $this->sessionMock = $this->createMock(SessionInterface::class);
        $this->userLocaleSubscriber = new UserLocaleSubscriber($this->sessionMock);
    }

    /**
     * @covers ::onInteractiveLogin
     */
    public function testOnInteractiveLogin()
    {
        $tokenMock = $this->createMock(TokenInterface::class);
        $tokenMock->expects($this->once())->method('getUser')->willReturn(new User());
        $eventMock = $this->createMock(InteractiveLoginEvent::class);
        $eventMock->expects($this->once())->method('getAuthenticationToken')->willReturn($tokenMock);

        $this->sessionMock->expects($this->once())->method('set');

        $this->userLocaleSubscriber->onInteractiveLogin($eventMock);
        $this->addToAssertionCount(1);
    }

    /**
     * @covers ::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertArrayHasKey(SecurityEvents::INTERACTIVE_LOGIN, UserLocaleSubscriber::getSubscribedEvents());
    }
}
