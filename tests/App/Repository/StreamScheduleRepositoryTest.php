<?php
declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\StreamSchedule;
use App\Exception\Repository\CouldNotModifyStreamScheduleException;
use App\Repository\StreamScheduleRepository;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Persisters\Entity\EntityPersister;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @coversDefaultClass \App\Repository\StreamScheduleRepository
 * @covers ::<!public>
 * @covers ::__construct()
 * @uses \App\Entity\StreamSchedule
 */
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
     * @throws CouldNotModifyStreamScheduleException
     * @covers ::save
     */
    public function testSaveSuccess()
    {
        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');
        $this->streamScheduleRepository->save(new StreamSchedule());

        $this->addToAssertionCount(1);
    }

    /**
     * @throws CouldNotModifyStreamScheduleException
     * @covers ::save
     */
    public function testSaveFailed()
    {
        $this->expectException(CouldNotModifyStreamScheduleException::class);

        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush')->willThrowException(new ORMException());
        $this->streamScheduleRepository->save(new StreamSchedule());
    }

    /**
     * @throws CouldNotModifyStreamScheduleException
     * @covers ::remove
     */
    public function testRemoveSuccess()
    {
        $this->entityManager->expects($this->once())->method('remove');
        $this->entityManager->expects($this->once())->method('flush');
        $this->streamScheduleRepository->remove(new StreamSchedule());

        $this->addToAssertionCount(1);
    }

    /**
     * @throws CouldNotModifyStreamScheduleException
     * @covers ::remove
     */
    public function testRemoveFailed()
    {
        $this->expectException(CouldNotModifyStreamScheduleException::class);

        $this->entityManager->expects($this->once())->method('remove');
        $this->entityManager->expects($this->once())->method('flush')->willThrowException(new ORMException());
        $this->streamScheduleRepository->remove(new StreamSchedule());
    }

    /**
     * @covers ::findActiveSchedules
     */
    public function testFindActiveSchedules()
    {
        $this->loadInvoked([new StreamSchedule()]);
        $streamSchedules = $this->streamScheduleRepository->findActiveSchedules();
        $this->assertInstanceOf(StreamSchedule::class, $streamSchedules[0]);
    }

    /**
     * @covers ::getScheduledItem
     */
    public function testGetScheduledItem()
    {
        $this->loadInvoked(new StreamSchedule());
        $streamSchedule = $this->streamScheduleRepository->getScheduledItem('id');
        $this->assertInstanceOf(StreamSchedule::class, $streamSchedule);
    }

    /**
     * @covers ::findRunningSchedule
     */
    public function testFindRunningSchedule()
    {
        $this->loadInvoked(new StreamSchedule());
        $schedule = $this->streamScheduleRepository->findRunningSchedule();
        $this->assertInstanceOf(StreamSchedule::class, $schedule);
    }

    private function loadInvoked($returnedValue)
    {
        $method = (is_array($returnedValue)) ? 'loadAll': 'load';
        $entityPersisterMock = $this->createMock(EntityPersister::class);
        $entityPersisterMock->expects($this->once())->method($method)->willReturn($returnedValue);
        $unitOfWorkMock = $this->createMock(UnitOfWork::class);
        $unitOfWorkMock->expects($this->once())->method('getEntityPersister')->willReturn($entityPersisterMock);
        $this->entityManager->expects($this->once())->method('getUnitOfWork')->willReturn($unitOfWorkMock);
    }
}
