<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use App\Validator\ContainsCronExpression;

/**
 * @ORM\Table(name="stream_schedule")
 * @ORM\Entity(repositoryClass="App\Repository\StreamScheduleRepository")
 */
class StreamSchedule
{
    /**
     * @var uuid|null
     * @ORM\Column(name="id", type="guid", nullable=false)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=50, unique=false)
     */
    private $name;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=50, unique=false)
     */
    private $command;

    /**
     * @ContainsCronExpression()
     * @var string|null
     * @ORM\Column(name="cron_expression", type="string", length=50, unique=false)
     */
    private $cronExpression;

    /**
     * @var \DateTimeImmutable|null
     * @ORM\Column(name="last_execution", type="datetime", nullable=true)
     */
    private $lastExecution;

    /**
     * @var bool|null
     * @ORM\Column(name="last_run_successful", type="boolean", nullable=true)
     */
    private $lastRunSuccessful;

    /**
     * @var int|null
     * @ORM\Column(type="integer")
     */
    private $priority;

    /**
     * @var bool|null
     * @ORM\Column(name="run_with_next_execution", type="boolean")
     */
    private $runWithNextExecution;

    /**
     * @var bool|null
     * @ORM\Column(name="disabled", type="boolean")
     */
    private $disabled;

    /**
     * @var bool|null
     * @ORM\Column(name="wrecked", type="boolean")
     */
    private $wrecked;

    /**
     * @return null|string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param null|string $id
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getCommand(): ?string
    {
        return $this->command;
    }

    /**
     * @param null|string $command
     */
    public function setCommand(?string $command): void
    {
        $this->command = $command;
    }

    /**
     * @return null|string
     */
    public function getCronExpression(): ?string
    {
        return $this->cronExpression;
    }

    /**
     * @param null|string $cronExpression
     */
    public function setCronExpression(?string $cronExpression): void
    {
        $this->cronExpression = $cronExpression;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getLastExecution(): ?\DateTimeImmutable
    {
        return $this->lastExecution;
    }

    /**
     * @param \DateTimeImmutable|null $lastExecution
     */
    public function setLastExecution(?\DateTimeImmutable $lastExecution): void
    {
        $this->lastExecution = $lastExecution;
    }

    /**
     * @return bool|null
     */
    public function getLastRunSuccessful(): ?bool
    {
        return $this->lastRunSuccessful;
    }

    /**
     * @param bool|null $lastRunSuccessful
     */
    public function setLastRunSuccessful(?bool $lastRunSuccessful): void
    {
        $this->lastRunSuccessful = $lastRunSuccessful;
    }

    /**
     * @return int|null
     */
    public function getPriority(): ?int
    {
        return $this->priority;
    }

    /**
     * @param int|null $priority
     */
    public function setPriority(?int $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * @return bool|null
     */
    public function getRunWithNextExecution(): ?bool
    {
        return $this->runWithNextExecution;
    }

    /**
     * @param bool|null $runWithNextExecution
     */
    public function setRunWithNextExecution(?bool $runWithNextExecution): void
    {
        $this->runWithNextExecution = $runWithNextExecution;
    }

    /**
     * @return bool|null
     */
    public function getDisabled(): ?bool
    {
        return $this->disabled;
    }

    /**
     * @param bool|null $disabled
     */
    public function setDisabled(?bool $disabled): void
    {
        $this->disabled = $disabled;
    }

    /**
     * @return bool|null
     */
    public function getWrecked(): ?bool
    {
        return $this->wrecked;
    }

    /**
     * @param bool|null $wrecked
     */
    public function setWrecked(?bool $wrecked): void
    {
        $this->wrecked = $wrecked;
    }
}
