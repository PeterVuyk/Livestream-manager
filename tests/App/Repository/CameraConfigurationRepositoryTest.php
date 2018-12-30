<?php
declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\CameraConfiguration;
use App\Repository\CameraConfigurationRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
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
     * @throws ORMException
     * @throws OptimisticLockException
     * @covers ::saveFromConfiguration
     */
    public function testSaveFromConfiguration()
    {
        $this->entityManager->expects($this->exactly(2))->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $this->cameraConfigurationRepository->saveFromConfiguration([new CameraConfiguration(), new CameraConfiguration()]);
        $this->addToAssertionCount(1);
    }
}
