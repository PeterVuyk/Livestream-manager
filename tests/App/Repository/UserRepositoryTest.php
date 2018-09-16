<?php
declare(strict_types=1);

namespace App\Tests\App\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\QueryBuilder;

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
     * @throws NonUniqueResultException
     */
    public function testLoadUserByUsername()
    {

        $queryMock = $this->createMock(AbstractQuery::class);
        $queryMock->expects($this->once())->method('getOneOrNullResult')->willReturn(new User());
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        $queryBuilderMock->expects($this->once())->method('select')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())->method('from')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())->method('where')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->exactly(2))->method('setParameter')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())->method('getQuery')->willReturn($queryMock);
        $this->entityManager->expects($this->once())->method('createQueryBuilder')->willReturn($queryBuilderMock);

        $user = $this->userRepository->loadUserByUsername('username');
        $this->assertInstanceOf(User::class, $user);
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
