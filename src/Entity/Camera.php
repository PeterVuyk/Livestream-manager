<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="camera", uniqueConstraints={@ORM\UniqueConstraint(name="camera", columns={"camera"})})
 * @ORM\Entity(repositoryClass="App\Repository\CameraRepository")
 */
class Camera implements StateAwareInterface
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="camera", length=50, type="string", nullable=false)
     */
    private $camera;

    /**
     * @var string
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $state;

    /**
     * @return string
     */
    public function getCamera(): string
    {
        return $this->camera;
    }

    /**
     * @param string $camera
     */
    public function setCamera(string $camera): void
    {
        $this->camera = $camera;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }
}
