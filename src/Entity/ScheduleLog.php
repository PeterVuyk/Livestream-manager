<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Table(name="schedule_log")
 * @ORM\Entity
 */
class ScheduleLog
{
    /**
     * @var Uuid
     * @ORM\Column(name="id", type="guid", nullable=false)
     * @ORM\Id
     */
    private $id;

    /**
     * @var ArrayCollection|StreamSchedule
     * @ORM\ManyToOne(targetEntity="StreamSchedule", inversedBy="scheduleLog")
     * @ORM\JoinColumn(name="stream_schedule_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $streamSchedule;

    /**
     * @var bool|null
     * @ORM\Column(name="last_run_successful", type="boolean", nullable=true)
     */
    private $runSuccessful;

    /**
     * @var string
     * @ORM\Column(name="message", type="text", nullable=false)
     */
    private $message;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(name="time_executed", type="datetime", nullable=false)
     */
    private $timeExecuted;

    /**
     * ScheduleLog constructor.
     * @param StreamSchedule|ArrayCollection $streamSchedule
     * @param bool $runSuccessful
     * @param string $message
     */
    public function __construct(
        StreamSchedule $streamSchedule,
        bool $runSuccessful,
        string $message
    ) {
        $this->id = Uuid::uuid4();
        $this->streamSchedule = $streamSchedule;
        $this->runSuccessful = $runSuccessful;
        $this->message = $message;
        $this->timeExecuted = new \DateTime();
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @return StreamSchedule|ArrayCollection
     */
    public function getStreamSchedule()
    {
        return $this->streamSchedule;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getTimeExecuted(): \DateTimeInterface
    {
        return $this->timeExecuted;
    }

    /**
     * @return bool|null
     */
    public function getRunSuccessful(): ?bool
    {
        return $this->runSuccessful;
    }
}
