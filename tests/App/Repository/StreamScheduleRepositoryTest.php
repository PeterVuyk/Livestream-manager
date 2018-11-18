<?php
declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\StreamSchedule;
use App\Repository\StreamScheduleRepository;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Persisters\Entity\EntityPersister;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\QueryBuilder;

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
        $this->loadInvoked([new StreamSchedule()]);
        $streamSchedules = $this->streamScheduleRepository->findActiveCommands();
        $this->assertInstanceOf(StreamSchedule::class, $streamSchedules[0]);
    }

    public function testGetRecurringScheduledItems()
    {
        $abstractQueryMock = $this->createMock(AbstractQuery::class);
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        $this->entityManager->expects($this->once())
            ->method('createQueryBuilder')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())->method('select')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())->method('from')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())->method('where')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->exactly(2))->method('andWhere')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())->method('getQuery')->willReturn($abstractQueryMock);
        $abstractQueryMock->expects($this->once())->method('getResult')->willReturn([new StreamSchedule()]);

        $streamSchedules =$this->streamScheduleRepository->getRecurringScheduledItems();
        $this->assertInstanceOf(StreamSchedule::class, $streamSchedules[0]);
    }

    public function testGetOnetimeScheduledItems()
    {
        $abstractQueryMock = $this->createMock(AbstractQuery::class);
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        $this->entityManager->expects($this->once())
            ->method('createQueryBuilder')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())->method('select')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())->method('from')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())->method('where')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->exactly(2))->method('andWhere')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())->method('getQuery')->willReturn($abstractQueryMock);
        $abstractQueryMock->expects($this->once())->method('getResult')->willReturn([new StreamSchedule()]);

        $streamSchedules =$this->streamScheduleRepository->getRecurringScheduledItems();
        $this->assertInstanceOf(StreamSchedule::class, $streamSchedules[0]);
    }

    public function testGetScheduledItem()
    {
        $this->loadInvoked(new StreamSchedule());
        $streamSchedule = $this->streamScheduleRepository->getScheduledItem('id');
        $this->assertInstanceOf(StreamSchedule::class, $streamSchedule);
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
