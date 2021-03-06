<?php
declare(strict_types=1);

namespace App\Entity;

use App\Exception\StreamSchedule\CouldNotGetExecutionEndTimeException;
use App\Exception\StreamSchedule\InvalidWeekdayException;
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
     * @assert\Choice(callback={"App\Entity\Weekday", "getDaysOfTheWeekKeys"})
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
     * @var string|null
     * @ORM\Column(type="string", length=100, unique=false)
     */
    private $channel;

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
     * @throws InvalidWeekdayException
     */
    public function setExecutionDay(int $executionDay): void
    {
        if (!Weekday::validate($executionDay)) {
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
        $this->streamDuration = $streamDuration;
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
    public function isRunning(): ?bool
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
     * @return string|null
     */
    public function getChannel(): ?string
    {
        return $this->channel;
    }

    /**
     * @param string|null $channel
     */
    public function setChannel(?string $channel): void
    {
        $this->channel = $channel;
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

        $nextExecution = new \DateTime(Weekday::getDayOfTheWeekById($this->getExecutionDay()));
        $nextExecution->modify($this->getExecutionTime()->format("H:i"));
        return $nextExecution;
    }

    /**
     * @return \DateTimeInterface|null
     * @throws CouldNotGetExecutionEndTimeException
     */
    public function getExecutionEndTime(): ?\DateTimeInterface
    {
        if ($this->isWrecked() || $this->isRunning() === false) {
            return null;
        }

        if (!is_int($this->getStreamDuration()) || !$this->getLastExecution() instanceof \DateTime) {
            return null;
        }

        try {
            $lastExecution = \DateTimeImmutable::createFromMutable($this->getLastExecution());
            return $lastExecution->modify(sprintf('+%s minutes', $this->getStreamDuration()));
        } catch (\Exception $exception) {
            throw CouldNotGetExecutionEndTimeException::forError($exception);
        }
    }

    /**
     * @return bool
     */
    public function streamTobeStarted(): bool
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
            if ($this->getNextExecutionTime() > new \DateTime('- 10 minutes')) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function streamToBeStopped(): bool
    {
        if ($this->isRunning() !== true) {
            return false;
        }
        if ($this->getExecutionEndTime() instanceof \DateTimeInterface &&
            $this->getExecutionEndTime()->getTimestamp() < (new \DateTime())->getTimestamp()) {
            return true;
        }
        return false;
    }
}
