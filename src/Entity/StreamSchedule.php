<?php
declare(strict_types=1);

namespace App\Entity;

use App\Exception\InvalidWeekdayException;
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
     * @assert\Choice(callback={"App\Entity\Weekdays", "getDaysOfTheWeekKeys"})
     * @var int|null
     * @ORM\Column(name="execution_day", type="integer", unique=false, nullable=true)
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
     * @ORM\OneToMany(targetEntity="App\Entity\ScheduleLog", mappedBy="streamSchedule", cascade={"persist", "remove"})
     * @ORM\OrderBy({"timeExecuted" = "ASC"})
     */
    private $scheduleLog = [];

    /**
     * @var int|null
     * @Assert\NotNull
     * @ORM\Column(name="stream_duration", type="integer", unique=false)
     */
    private $streamDuration;

    /**
     * @var bool|null
     * @ORM\Column(name="is_running", type="boolean")
     */
    private $isRunning;

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
     * @return int|null
     */
    public function getExecutionDay(): ?int
    {
        return $this->executionDay;
    }

    /**
     * @param int $executionDay
     * @throws \InvalidArgumentException
     */
    public function setExecutionDay(int $executionDay): void
    {
        if (!Weekdays::validate($executionDay)) {
            throw InvalidWeekdayException::invalidDayInput($executionDay);
        }
        $this->executionDay = $executionDay;
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
     * @return null|int
     */
    public function getStreamDuration(): ?int
    {
        return $this->streamDuration;
    }

    /**
     * @param null|int $streamDuration
     */
    public function setStreamDuration(?int $streamDuration): void
    {
        $this->streamDuration= $streamDuration;
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
     * @return bool|null
     */
    public function IsRunning(): ?bool
    {
        return $this->isRunning;
    }

    /**
     * @param bool|null $isRunning
     */
    public function setIsRunning(?bool $isRunning): void
    {
        $this->isRunning = $isRunning;
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

        $nextExecution = new \DateTime(WeekDays::getDayOfTheWeekById($this->getExecutionDay()));
        $nextExecution->modify($this->getExecutionTime()->format("H:i"));
        return $nextExecution;
    }

    /**
     * @return \DateTime|null
     * @throws \Exception
     */
    public function getExecutionEndTime(): ?\DateTime
    {
        if ($this->isWrecked() || $this->isRunning() === false) {
            return null;
        }

        if (!is_int($this->getStreamDuration()) || !$this->getLastExecution() instanceof \DateTime) {
            return null;
        }

        return date_add($this->getLastExecution(), new \DateInterval('PT' . $this->getStreamDuration() . 'M'));
    }

    /**
     * @return bool
     */
    public function streamTobeExecuted(): bool
    {
        if ($this->isWrecked() === true) {
            return false;
        }
        if ($this->getLastExecution() instanceof \DateTime) {
            if ($this->getLastExecution() > new \DateTime('- 1 hour')) {
                return false;
            }
        }
        if ($this->getNextExecutionTime() <= new \DateTime()) {
            if ($this->getNextExecutionTime() > new \DateTime('- 15 minutes')) {
                return true;
            }
        }
        return false;
    }
}
