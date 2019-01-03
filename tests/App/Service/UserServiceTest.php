<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\User;
use App\Exception\UserNotFoundException;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @coversDefaultClass \App\Service\UserService
 * @covers ::<!public>
 * @covers ::__construct()
 * @uses \App\Entity\User
 */
class UserServiceTest extends TestCase
{
    /** @var UserService */
    private $userService;

    /** @var MockObject|UserPasswordEncoderInterface */
    private $passwordEncoderMock;

    /** @var MockObject|UserRepository */
    private $userRepositoryMock;

    public function setUp()
    {
        $this->passwordEncoderMock = $this->createMock(UserPasswordEncoderInterface::class);
        $this->userRepositoryMock = $this->createMock(UserRepository::class);
        $this->userService = new UserService($this->passwordEncoderMock, $this->userRepositoryMock);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UserNotFoundException
     * @covers ::toggleDisablingUser
     * @uses \App\Service\UserService
     */
    public function testToggleDisablingUserSuccess()
    {
        $user = new User();
        $user->setEnabled(true);
        $this->userRepositoryMock->expects($this->once())->method('findOneBy')->willReturn($user);
        $this->userRepositoryMock->expects($this->once())->method('save');

        $this->userService->toggleDisablingUser(3);
        $this->addToAssertionCount(1);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UserNotFoundException
     * @covers ::toggleDisablingUser
     * @uses \App\Service\UserService
     */
    public function testToggleDisablingUserNoUserFound()
    {
        $this->expectException(UserNotFoundException::class);

        $this->userRepositoryMock->expects($this->once())->method('findOneBy')->willReturn(null);

        $this->userService->toggleDisablingUser(4);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UserNotFoundException
     * @covers ::removeUser
     * @uses \App\Service\UserService
     */
    public function testRemoveUserSuccess()
    {
        $this->userRepositoryMock->expects($this->once())->method('findOneBy')->willReturn(new User());
        $this->userRepositoryMock->expects($this->once())->method('remove');

        $this->userService->removeUser(3);
        $this->addToAssertionCount(1);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UserNotFoundException
     * @covers ::removeUser
     * @uses \App\Service\UserService
     */
    public function testRemoveUserNoUserFound()
    {
        $this->expectException(UserNotFoundException::class);

        $this->userRepositoryMock->expects($this->once())->method('findOneBy')->willReturn(null);

        $this->userService->removeUser(4);
    }

    /**
     * @covers ::getAllUsers
     */
    public function testGetAllUsers()
    {
        $this->userRepositoryMock->expects($this->once())->method('findAll')->willReturn([new User()]);
        $this->assertInstanceOf(User::class, $this->userService->getAllUsers()[0]);
    }

    /**
     * @covers ::getUserById
     */
    public function testGetUserById()
    {
        $this->userRepositoryMock->expects($this->once())->method('findOneBy')->willReturn(new User());
        $this->assertInstanceOf(User::class, $this->userService->getUserById(33));
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @covers ::createUser
     */
    public function testCreateUserSuccess()
    {
        $this->passwordEncoderMock->expects($this->once())->method('encodePassword')->willReturn('password');
        $this->userRepositoryMock->expects($this->once())->method('save');
        $this->userService->createUser(new User());
        $this->addToAssertionCount(1);
    }
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @covers ::createUser
     */
    public function testCreateUserFailed()
    {
        $this->expectException(ORMException::class);
        $this->passwordEncoderMock->expects($this->once())->method('encodePassword')->willReturn('password');
        $this->userRepositoryMock->expects($this->once())->method('save')->willThrowException(new ORMException());
        $this->userService->createUser(new User());
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @covers ::updateUser
     */
    public function testUpdateUser()
    {
        $this->userRepositoryMock->expects($this->once())->method('save');
        $this->userService->updateUser(new User());
        $this->addToAssertionCount(1);
    }
}
