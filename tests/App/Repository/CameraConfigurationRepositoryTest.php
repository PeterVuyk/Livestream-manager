<?php
declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\CameraConfiguration;
use App\Exception\Repository\CouldNotModifyCameraConfigurationException;
use App\Repository\CameraConfigurationRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @coversDefaultClass \App\Repository\CameraConfigurationRepository
 * @covers ::<!public>
 * @covers ::__construct()
 * @uses \App\Entity\CameraConfiguration
 */
class CameraConfigurationRepositoryTest extends TestCase
{
    /** @var CameraConfigurationRepository */
    private $cameraConfigurationRepository;

    /** @var MockObject|EntityManager */
    private $entityManager;

    public function setUp()
    {
        $classMetaData = $this->createMock(ClassMetadata::class);
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->entityManager->expects($this->once())->method('getClassMetadata')->willReturn($classMetaData);
        $registryInterface = $this->createMock(RegistryInterface::class);
        $registryInterface->expects($this->once())->method('getManagerForClass')->willReturn($this->entityManager);
        $this->cameraConfigurationRepository = new CameraConfigurationRepository($registryInterface);
    }

    /**
     * @throws CouldNotModifyCameraConfigurationException
     * @covers ::saveFromConfiguration
     */
    public function testSaveFromConfigurationSuccess()
    {
        $this->entityManager->expects($this->exactly(2))->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $this->cameraConfigurationRepository->saveFromConfiguration([new CameraConfiguration(), new CameraConfiguration()]);
        $this->addToAssertionCount(1);
    }

    /**
     * @throws CouldNotModifyCameraConfigurationException
     * @covers ::saveFromConfiguration
     */
    public function testSaveFromConfigurationFailed()
    {
        $this->expectException(CouldNotModifyCameraConfigurationException::class);

        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush')->willThrowException(new ORMException());

        $this->cameraConfigurationRepository->saveFromConfiguration([new CameraConfiguration()]);
    }
}
