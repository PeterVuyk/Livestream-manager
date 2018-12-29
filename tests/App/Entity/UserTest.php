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
}
