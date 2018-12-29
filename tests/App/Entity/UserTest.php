<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserCreation()
    {
        $user = new User();
        $this->assertInstanceOf(User::class, $user);
        $this->assertContains(User::ROLE_USER, $user->getRoles());
        $this->assertTrue($user->isEnabled());
    }

    public function testDefaultLocale()
    {
        $user = new User();
        $this->assertSame('en', $user->getLocale());
    }

    public function testUpdateLocale()
    {
        $user = new User();
        $user->setLocale('nl');
        $this->assertSame('nl', $user->getLocale());
    }
}
