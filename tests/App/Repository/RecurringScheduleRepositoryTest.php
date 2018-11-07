<?php
declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\RecurringSchedule;
use App\Repository\RecurringScheduleRepository;
use Doctrine\ORM\Persisters\Entity\EntityPersister;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bridge\Doctrine\RegistryInterface;

class RecurringScheduleRepositoryTest extends TestCase
{
    /** @var RecurringScheduleRepository */
    private $recurringScheduleRepository;

    /** @var MockObject|EntityManager */
    private $entityManager;

    public function setUp()
    {
        $classMetaData = $this->createMock(ClassMetadata::class);
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->entityManager->expects($this->once())->method('getClassMetadata')->willReturn($classMetaData);
        $registryInterface = $this->createMock(RegistryInterface::class);
        $registryInterface->expects($this->once())->method('getManagerForClass')->willReturn($this->entityManager);
        $this->recurringScheduleRepository = new RecurringScheduleRepository($registryInterface);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testSave()
    {
        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');
        $this->recurringScheduleRepository->save(new RecurringSchedule());
    }

    /**
     * @throws ORMException
     */
    public function testRemove()
    {
        $this->entityManager->expects($this->once())->method('remove');
        $this->entityManager->expects($this->once())->method('flush');
        $this->recurringScheduleRepository->remove(new RecurringSchedule());
    }

    public function testFindActiveCommands()
    {
        $entityPersisterMock = $this->createMock(EntityPersister::class);
        $entityPersisterMock->expects($this->once())->method('loadAll')->willReturn([new RecurringSchedule()]);
        $unitOfWorkMock = $this->createMock(UnitOfWork::class);
        $unitOfWorkMock->expects($this->once())->method('getEntityPersister')->willReturn($entityPersisterMock);
        $this->entityManager->expects($this->once())->method('getUnitOfWork')->willReturn($unitOfWorkMock);

        $recurringSchedules = $this->recurringScheduleRepository->findActiveCommands();
        $this->assertInstanceOf(RecurringSchedule::class, $recurringSchedules[0]);
    }
}
