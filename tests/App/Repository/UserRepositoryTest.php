<?php
declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\User;
use App\Exception\Repository\CouldNotModifyUserException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @coversDefaultClass \App\Repository\UserRepository
 * @covers ::<!public>
 * @covers ::__construct()
 * @uses \App\Entity\User
 */
class UserRepositoryTest extends TestCase
{
    /** @var UserRepository */
    private $userRepository;

    /** @var MockObject|EntityManager */
    private $entityManager;

    public function setUp()
    {
        $classMetaData = $this->createMock(ClassMetadata::class);
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->entityManager->expects($this->once())->method('getClassMetadata')->willReturn($classMetaData);
        $registryInterface = $this->createMock(RegistryInterface::class);
        $registryInterface->expects($this->once())->method('getManagerForClass')->willReturn($this->entityManager);
        $this->userRepository = new UserRepository($registryInterface);
    }

    /**
     * @throws CouldNotModifyUserException
     * @covers ::save
     */
    public function testSaveSuccess()
    {
        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');
        $this->userRepository->save(new User());

        $this->addToAssertionCount(1);
    }

    /**
     * @throws CouldNotModifyUserException
     * @covers ::save
     */
    public function testSaveFailed()
    {
        $this->expectException(CouldNotModifyUserException::class);

        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush')->willThrowException(new ORMException());
        $this->userRepository->save(new User());
    }

    /**
     * @throws CouldNotModifyUserException
     * @covers ::remove
     */
    public function testRemoveSuccess()
    {
        $this->entityManager->expects($this->once())->method('remove');
        $this->entityManager->expects($this->once())->method('flush');
        $this->userRepository->remove(new User());

        $this->addToAssertionCount(1);
    }

    /**
     * @throws CouldNotModifyUserException
     * @covers ::remove
     */
    public function testRemoveFailed()
    {
        $this->expectException(CouldNotModifyUserException::class);

        $this->entityManager->expects($this->once())->method('remove');
        $this->entityManager->expects($this->once())->method('flush')->willThrowException(new ORMException());
        $this->userRepository->remove(new User());
    }
}
