<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\CameraConfiguration;
use App\Entity\Configuration;
use App\Exception\CouldNotModifyCameraConfigurationException;
use App\Repository\CameraConfigurationRepository;
use App\Service\CameraConfigurationService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @coversDefaultClass \App\Service\CameraConfigurationService
 * @covers ::<!public>
 * @covers ::__construct()
 * @uses \App\Entity\CameraConfiguration
 * @uses \App\Entity\Configuration
 */
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

    /**
     * @covers ::getAllConfigurations
     */
    public function testGetAllConfigurations()
    {
        $this->cameraConfigurationRepositoryMock->expects($this->once())
            ->method('findAll')
            ->willReturn([new CameraConfiguration()]);

        $result = $this->cameraConfigurationService->getAllConfigurations();
        $this->assertInstanceOf(CameraConfiguration::class, $result[0]);
    }

    /**
     * @throws CouldNotModifyCameraConfigurationException
     * @covers ::saveConfigurations
     */
    public function testSaveConfigurations()
    {
        $this->cameraConfigurationRepositoryMock->expects($this->once())->method('saveFromConfiguration');
        $this->cameraConfigurationService->saveConfigurations(new Configuration([new CameraConfiguration()]));
    }

    /**
     * @covers ::getConfigurationsKeyValue
     * @uses \App\Service\CameraConfigurationService
     */
    public function testGetConfigurationsKeyValue()
    {
        $cameraConfiguration1 = new CameraConfiguration();
        $cameraConfiguration1->setValue('value1');
        $cameraConfiguration1->setKey('key1');
        $cameraConfiguration2 = new CameraConfiguration();
        $cameraConfiguration2->setValue('value2');
        $cameraConfiguration2->setKey('key2');

        $this->cameraConfigurationRepositoryMock->expects($this->once())
            ->method('findAll')
            ->willReturn([$cameraConfiguration1, $cameraConfiguration2]);
        $configurations = $this->cameraConfigurationService->getConfigurationsKeyValue();

        $this->assertObjectHasAttribute('key1', $configurations);
        $this->assertObjectHasAttribute('key2', $configurations);
        $this->assertSame('value1', $configurations->key1);
        $this->assertSame('value2', $configurations->key2);
    }
}
