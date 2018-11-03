<?php
declare(strict_types=1);

namespace App\Tests\App\Repository;

use App\Entity\StreamSchedule;
use App\Repository\StreamScheduleRepository;
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
}
