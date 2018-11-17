<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @assert\Choice({"monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"})
     * @var string|null
     * @ORM\Column(name="execution_day", type="string", unique=false, nullable=true)
     */
    private $executionDay;

    /**
     * @var mixed|null
     * @ORM\Column(name="execution_time", type="time", unique=false, nullable=true)
     */
    private $executionTime;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="onetime_execution_date", type="datetime", unique=false, nullable=true)
     */
    private $onetimeExecutionDate;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="last_execution", type="datetime", nullable=true)
     */
    private $lastExecution;

    /**
     * @Assert\GreaterThan(0)
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
     * @var ArrayCollection|ScheduleLog[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\ScheduleLog", mappedBy="streamSchedule", cascade={"persist"})
     * @ORM\OrderBy({"timeExecuted" = "ASC"})
     */
    private $scheduleLog = [];

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
     * @return \DateTime|null
     */
    public function getLastExecution(): ?\DateTime
    {
        return $this->lastExecution;
    }

    /**
     * @param \DateTime|null $lastExecution
     */
    public function setLastExecution(?\DateTime $lastExecution): void
    {
        $this->lastExecution = $lastExecution;
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
    public function isWrecked(): ?bool
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

    /**
     * @return ScheduleLog[]|ArrayCollection
     */
    public function getScheduleLog()
    {
        return $this->scheduleLog;
    }

    /**
     * @param ScheduleLog|ArrayCollection $scheduleLog
     */
    public function addScheduleLog(ScheduleLog $scheduleLog): void
    {
        $this->scheduleLog[] = $scheduleLog;
    }

    /**
     * @return string|null
     */
    public function getExecutionDay(): ?string
    {
        return $this->executionDay;
    }

    /**
     * @param string $executionDay
     * @throws \InvalidArgumentException
     */
    public function setExecutionDay(string $executionDay): void
    {
        $day = strtolower($executionDay);
        if (!in_array($day, Weekdays::getDaysOfTheWeek())) {
            throw new \InvalidArgumentException('Invalid executionDay input');
        }
        $this->executionDay = $day;
    }

    /**
     * @return \DateTime|null
     */
    public function getExecutionTime(): ?\DateTime
    {
        return $this->executionTime;
    }

    /**
     * @param \DateTime|null $executionTime
     */
    public function setExecutionTime(?\DateTime $executionTime): void
    {
        $this->executionTime = $executionTime;
    }

    /**
     * @return \DateTime|null
     */
    public function getOnetimeExecutionDate(): ?\DateTime
    {
        return $this->onetimeExecutionDate;
    }

    /**
     * @param \DateTime|null $onetimeExecutionDate
     */
    public function setOnetimeExecutionDate(?\DateTime $onetimeExecutionDate): void
    {
        $this->onetimeExecutionDate = $onetimeExecutionDate;
    }

    /**
     * @return bool
     */
    public function isRecurring(): bool
    {
        if ($this->getOnetimeExecutionDate() instanceof \DateTime) {
            return false;
        }
        return true;
    }

    /**
     * @return \DateTime|null
     */
    public function getNextExecutionTime(): ?\DateTime
    {
        if ($this->getOnetimeExecutionDate() instanceof \DateTime) {
            return $this->getOnetimeExecutionDate();
        }

        if (empty($this->getExecutionDay() || !$this->getExecutionTime() instanceof \DateTime)) {
            return null;
        }

        $nextExecution = new \DateTime($this->getExecutionDay());
        $nextExecution->modify($this->getExecutionTime()->format("H:i"));
        return $nextExecution;
    }

    /**

     * @return bool
     */
    public function streamTobeExecuted(): bool
    {
        if ($this->isWrecked() === true) {
            return false;
        }
        if ($this->getRunWithNextExecution() === true) {
            return true;
        }
        if ($this->getLastExecution() instanceof \DateTime) {
            if ($this->getLastExecution() > new \DateTime('- 1 hour')) {
                return false;
            }
        }
        if ($this->getNextExecutionTime() < new \DateTime()) {
            return true;
        }
        return false;
    }
}
