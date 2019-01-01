<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Configuration
{
    /** @var ArrayCollection */
    private $cameraConfiguration;

    /**
     * Configuration constructor.
     * @param CameraConfiguration[] $cameraConfigurations
     */
    public function __construct(array $cameraConfigurations)
    {
        $this->cameraConfiguration = new ArrayCollection();
        foreach ($cameraConfigurations as $cameraConfiguration) {
            $this->getCameraConfiguration()->add($cameraConfiguration);
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getCameraConfiguration()
    {
        return $this->cameraConfiguration;
    }
}
