<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="channel", uniqueConstraints={@ORM\UniqueConstraint(name="channel_name", columns={"channel_name"})})
 * @ORM\Entity(repositoryClass="App\Repository\ChannelRepository")
 */
class Channel
{
    /**
     * @var string|null
     * @Assert\NotEqualTo("admin")
     * @Assert\NotEqualTo("Admin")
     * @ORM\Id
     * @ORM\Column(name="channel_name", length=100, type="string", nullable=false)
     */
    private $channelName;

    /**
     * @var string|null
     * @ORM\Column(name="user_name", length=100, type="string", nullable=false)
     */
    private $userName;

    /**
     * @var string|null
     * @ORM\Column(name="host", length=255, type="string", nullable=false)
     */
    private $host;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=64)
     */
    private $secret;

    /**
     * @return string|null
     */
    public function getChannelName(): ?string
    {
        return $this->channelName;
    }

    /**
     * @param string|null $channelName
     */
    public function setChannelName(?string $channelName): void
    {
        $this->channelName = $channelName;
    }

    /**
     * @return string|null
     */
    public function getUserName(): ?string
    {
        return $this->userName;
    }

    /**
     * @param string|null $userName
     */
    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    /**
     * @return string|null
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * @param string|null $host
     */
    public function setHost(?string $host): void
    {
        $this->host = $host;
    }

    /**
     * @return string|null
     */
    public function getSecret(): ?string
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     */
    public function setSecret(string $secret): void
    {
        $this->secret = $secret;
    }
}
