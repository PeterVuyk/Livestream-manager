<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Entity\User
 * @covers ::<!public>
 * @covers ::__construct()
 */
class UserTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testUserCreation()
    {
        $user = new User();
        $this->assertInstanceOf(User::class, $user);
        $this->assertContains(User::ROLE_USER, $user->getRoles());
        $this->assertTrue($user->isEnabled());
    }

    /**
     * @covers ::getLocale
     */
    public function testDefaultLocale()
    {
        $user = new User();
        $this->assertSame('en', $user->getLocale());
    }

    /**
     * @covers ::setLocale
     * @covers ::getLocale
     */
    public function testUpdateLocale()
    {
        $user = new User();
        $user->setLocale('nl');
        $this->assertSame('nl', $user->getLocale());
    }

    /**
     * @covers ::setChannel
     * @covers ::getChannel
     */
    public function testChannel()
    {
        $user = new User();
        $user->setChannel('name');
        $this->assertSame('name', $user->getChannel());
    }
}
