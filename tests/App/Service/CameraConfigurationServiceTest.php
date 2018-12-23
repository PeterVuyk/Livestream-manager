<?php
declare(strict_types=1);

namespace App\Tests\App\Service;

use App\Entity\CameraConfiguration;
use App\Entity\Configuration;
use App\Repository\CameraConfigurationRepository;
use App\Service\CameraConfigurationService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class CameraConfigurationServiceTest extends TestCase
{
    /** @var CameraConfigurationService|MockObject */
    private $cameraConfigurationRepositoryMock;

    /** @var LoggerInterface|MockObject */
    private $loggerMock;

    /** @var CameraConfigurationService */
    private $cameraConfigurationService;

    public function setUp()
    {
        $this->cameraConfigurationRepositoryMock = $this->createMock(CameraConfigurationRepository::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->cameraConfigurationService = new CameraConfigurationService(
            $this->cameraConfigurationRepositoryMock,
            $this->loggerMock
        );
    }

    public function testGetConfigurations()
    {
        $this->cameraConfigurationRepositoryMock->expects($this->once())
            ->method('findAll')
            ->willReturn([new CameraConfiguration()]);

        $result = $this->cameraConfigurationService->getAllConfigurations();
        $this->assertInstanceOf(CameraConfiguration::class, $result[0]);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testSaveConfigurations()
    {
        $this->cameraConfigurationRepositoryMock->expects($this->once())->method('saveFromConfiguration');
        $this->cameraConfigurationService->saveConfigurations(new Configuration());
    }
}
