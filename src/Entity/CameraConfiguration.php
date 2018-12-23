<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="camera_configuration", uniqueConstraints={@ORM\UniqueConstraint(name="key", columns={"key"})})
 * @ORM\Entity(repositoryClass="App\Repository\CameraConfigurationRepository")
 */
class CameraConfiguration
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="`key`", type="string", nullable=false)
     */
    private $key;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", nullable=false)
     */
    private $value;

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}
