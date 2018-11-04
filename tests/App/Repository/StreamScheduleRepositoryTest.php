<?php
declare(strict_types=1);

namespace App\Tests\App\Repository;

use App\Entity\StreamSchedule;
use App\Repository\StreamScheduleRepository;
use Doctrine\ORM\Persisters\Entity\EntityPersister;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bridge\Doctrine\RegistryInterface;

class StreamScheduleRepositoryTest extends TestCase
{
    /** @var StreamScheduleRepository */
    private $streamScheduleRepository;

    /** @var MockObject|EntityManager */
    private $entityManager;

    public function setUp()
    {
        $classMetaData = $this->createMock(ClassMetadata::class);
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->entityManager->expects($this->once())->method('getClassMetadata')->willReturn($classMetaData);
        $registryInterface = $this->createMock(RegistryInterface::class);
        $registryInterface->expects($this->once())->method('getManagerForClass')->willReturn($this->entityManager);
        $this->streamScheduleRepository = new StreamScheduleRepository($registryInterface);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testSave()
    {
        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');
        $this->streamScheduleRepository->save(new StreamSchedule());
    }

    /**
     * @throws ORMException
     */
    public function testRemove()
    {
        $this->entityManager->expects($this->once())->method('remove');
        $this->entityManager->expects($this->once())->method('flush');
        $this->streamScheduleRepository->remove(new StreamSchedule());
    }

    public function testFindActiveCommands()
    {
        $entityPersisterMock = $this->createMock(EntityPersister::class);
        $entityPersisterMock->expects($this->once())->method('loadAll')->willReturn([new StreamSchedule()]);
        $unitOfWorkMock = $this->createMock(UnitOfWork::class);
        $unitOfWorkMock->expects($this->once())->method('getEntityPersister')->willReturn($entityPersisterMock);
        $this->entityManager->expects($this->once())->method('getUnitOfWork')->willReturn($unitOfWorkMock);

        $streamSchedules = $this->streamScheduleRepository->findActiveCommands();
        $this->assertInstanceOf(StreamSchedule::class, $streamSchedules[0]);
    }
}
