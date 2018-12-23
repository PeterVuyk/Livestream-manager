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
     */
    public function __construct()
    {
        $this->cameraConfiguration = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getCameraConfiguration()
    {
        return $this->cameraConfiguration;
    }
}
