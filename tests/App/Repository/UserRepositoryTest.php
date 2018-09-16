<?php
declare(strict_types=1);

namespace App\Tests\App\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    /** @var UserRepository */
    private $userRepository;

    /** @var MockObject|EntityManager */
    private $entityManager;

    /** @var MockObject|ClassMetadata */
    private $classMetaData;

    public function setUp()
    {
        $entityManager = $this->entityManager = $this->createMock(EntityManager::class);
        $classMetaData = $this->classMetaData = $this->createMock(ClassMetadata::class);
        $this->userRepository = new UserRepository($entityManager, $classMetaData);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testSave()
    {
        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');
        $this->userRepository->save(new User());
        $this->addToAssertionCount(1);
    }
}
