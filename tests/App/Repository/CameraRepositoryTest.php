<?php
declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\Camera;
use App\Exception\CouldNotModifyCameraConfigurationException;
use App\Exception\CouldNotModifyCameraException;
use App\Repository\CameraRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Persisters\Entity\EntityPersister;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @coversDefaultClass \App\Repository\CameraRepository
 * @covers ::<!public>
 * @covers ::__construct()
 * @uses \App\Entity\Camera
 */
class CameraRepositoryTest extends TestCase
{
    /** @var CameraRepository */
    private $cameraRepository;

    /** @var MockObject|EntityManager */
    private $entityManager;

    public function setUp()
    {
        $classMetaData = $this->createMock(ClassMetadata::class);
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->entityManager->expects($this->once())->method('getClassMetadata')->willReturn($classMetaData);
        $registryInterface = $this->createMock(RegistryInterface::class);
        $registryInterface->expects($this->once())->method('getManagerForClass')->willReturn($this->entityManager);
        $this->cameraRepository = new CameraRepository($registryInterface);
    }

    /**
     * @covers ::getMainCamera
     */
    public function testGetMainCamera()
    {
        $entityPersister = $this->createMock(EntityPersister::class);
        $entityPersister->expects($this->once())->method('load')->willReturn(new Camera());
        $unitOfWork = $this->createMock(UnitOfWork::class);
        $unitOfWork->expects($this->once())->method('getEntityPersister')->willReturn($entityPersister);
        $this->entityManager->expects($this->once())->method('getUnitOfWork')->willReturn($unitOfWork);

        $this->assertInstanceOf(Camera::class, $this->cameraRepository->getMainCamera());
    }

    /**
     * @throws CouldNotModifyCameraException
     * @covers ::save
     */
    public function testSaveSuccess()
    {
        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $this->cameraRepository->save(new Camera());
        $this->addToAssertionCount(1);
    }

    /**
     * @throws CouldNotModifyCameraException
     * @covers ::save
     */
    public function testSaveFailed()
    {
        $this->expectException(CouldNotModifyCameraConfigurationException::class);

        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())
            ->method('flush')
            ->willThrowException(CouldNotModifyCameraConfigurationException::forError(new ORMException()));

        $this->cameraRepository->save(new Camera());
    }
}
