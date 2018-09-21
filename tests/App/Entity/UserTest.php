<?php
declare(strict_types=1);

namespace App\Tests\App\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserName()
    {
        $user = new User();
        $user->setUsername('userName');
        $this->assertSame('userName', $user->getUsername());
    }

    public function testEmail()
    {
        $user = new User();
        $user->setEmail('some@mail.nl');
        $this->assertSame('some@mail.nl', $user->getEmail());
    }

    public function testId()
    {
        $user = new User();
        $user->setId(3);
        $this->assertSame(3, $user->getId());
    }

    public function testIsActive()
    {
        $user = new User();
        $user->setActive(true);
        $this->assertSame(true, $user->isActive());
    }

    public function testPassword()
    {
        $user = new User();
        $user->setPassword('very-secret-password');
        $this->assertSame('very-secret-password', $user->getPassword());
    }

    public function testPlainPassword()
    {
        $user = new User();
        $user->setPlainPassword('plain!');
        $this->assertSame('plain!', $user->getPlainPassword());
    }

    public function testRoles()
    {
        $user = new User();
        $user->setRoles('ROLE_USER');
        $this->assertSame('ROLE_USER', $user->getRoles());
    }

    public function testSerialize()
    {
        $this->assertSame(
            'a:4:{i:0;i:3;i:1;s:8:"userName";i:2;s:12:"some@mail.nl";i:3;s:4:"hash";}',
            $this->getUser()->serialize()
        );
    }

    public function testUnserialize()
    {
        $user = new User();
        $user->unserialize('a:4:{i:0;i:3;i:1;s:8:"userName";i:2;s:12:"some@mail.nl";i:3;s:4:"hash";}');
        $this->addToAssertionCount(1);
    }

    /**
     * @return User
     */
    private function getUser()
    {
        $user = new User();
        $user->setEmail('some@mail.nl');
        $user->setId(3);
        $user->setActive(true);
        $user->setPassword('hash');
        $user->setPlainPassword('plain-password');
        $user->setRoles('ROLE_USER');
        $user->setUsername('userName');
        return $user;
    }
}
