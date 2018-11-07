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

class UserServiceTest extends TestCase
{
    /** @var UserService */
    private $userService;

    /** @var MockObject|UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var MockObject|UserRepository */
    private $userRepository;

    public function setUp()
    {
        $passwordEncoder = $this->passwordEncoder = $this->createMock(UserPasswordEncoderInterface::class);
        $userRepository = $this->userRepository = $this->createMock(UserRepository::class);
        $this->userService = new UserService($passwordEncoder, $userRepository);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UserNotFoundException
     */
    public function testToggleDisablingUserSuccess()
    {
        $user = new User();
        $user->setActive(true);
        $this->userRepository->expects($this->once())->method('findOneBy')->willReturn($user);
        $this->userRepository->expects($this->once())->method('save');

        $this->userService->toggleDisablingUser(3);
        $this->addToAssertionCount(1);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UserNotFoundException
     */
    public function testToggleDisablingUserNoUserFound()
    {
        $this->expectException(UserNotFoundException::class);

        $this->userRepository->expects($this->once())->method('findOneBy')->willReturn(null);

        $this->userService->toggleDisablingUser(4);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UserNotFoundException
     */
    public function testRemoveUserSuccess()
    {
        $this->userRepository->expects($this->once())->method('findOneBy')->willReturn(new User());
        $this->userRepository->expects($this->once())->method('remove');

        $this->userService->removeUser(3);
        $this->addToAssertionCount(1);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UserNotFoundException
     */
    public function testRemoveUserNoUserFound()
    {
        $this->expectException(UserNotFoundException::class);

        $this->userRepository->expects($this->once())->method('findOneBy')->willReturn(null);

        $this->userService->removeUser(4);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testCreateUserSuccess()
    {
        $this->passwordEncoder->expects($this->once())->method('encodePassword')->willReturn('password');
        $this->userRepository->expects($this->once())->method('save');
        $this->userService->createUser(new User());
        $this->addToAssertionCount(1);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testCreateUserFailed()
    {
        $this->expectException(ORMException::class);
        $this->passwordEncoder->expects($this->once())->method('encodePassword')->willReturn('password');
        $this->userRepository->expects($this->once())->method('save')->willThrowException(new ORMException());
        $this->userService->createUser(new User());
    }

    public function testGetAllUsers()
    {
        $this->userRepository->expects($this->once())->method('findAll')->willReturn([new User()]);
        $this->assertInstanceOf(User::class, $this->userService->getAllUsers()[0]);
    }

    public function testGetUserById()
    {
        $this->userRepository->expects($this->once())->method('findOneBy')->willReturn(new User());
        $this->assertInstanceOf(User::class, $this->userService->getUserById(33));
    }
}
