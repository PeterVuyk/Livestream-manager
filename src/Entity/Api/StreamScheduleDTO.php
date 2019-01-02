<?php
declare(strict_types=1);

namespace App\Entity\Api;

use App\Entity\StreamSchedule;
use App\Entity\Weekday;
use Webmozart\Assert\Assert;

class StreamScheduleDTO
{
    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var string|null */
    private $executionDay;

    /** @var string|null */
    private $executionTime;

    /** @var \DateTime|null */
    private $onetimeExecutionDate;

    /** @var int */
    private $minutesStreamDuration;

    /** @var bool */
    private $isRunning;

    /** @var bool */
    private $isRecurring;

    /** @var \DateTime */
    private $nextExecutionTime;

    /**
     * StreamScheduleDTO constructor.
     * @throws \InvalidArgumentException
     * @param StreamSchedule $streamSchedule
     */
    private function __construct(StreamSchedule $streamSchedule)
    {
        $this->id = $streamSchedule->getId();
        $this->name = $streamSchedule->getName();
        $this->executionDay =
            $streamSchedule->getExecutionDay()? Weekday::getDayOfTheWeekById($streamSchedule->getExecutionDay()) : null;
        $this->executionTime =
            $streamSchedule->getExecutionTime()? $streamSchedule->getExecutionTime()->format('H:i:s') : null;
        $this->onetimeExecutionDate = $streamSchedule->getOnetimeExecutionDate();
        $this->minutesStreamDuration = $streamSchedule->getStreamDuration();
        $this->isRunning = $streamSchedule->isRunning();
        $this->isRecurring = $streamSchedule->isRecurring();
        $this->nextExecutionTime = $streamSchedule->getNextExecutionTime();
    }

    /**
     * @param StreamSchedule $streamSchedule
     * @return StreamScheduleDTO
     */
    public static function createFromStreamSchedule(StreamSchedule $streamSchedule)
    {
        self::validate($streamSchedule);
        return new self($streamSchedule);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return null|string
     */
    public function getExecutionDay(): ?string
    {
        return $this->executionDay;
    }

    /**
     * @return string|null
     */
    public function getExecutionTime(): ?string
    {
        return $this->executionTime;
    }

    /**
     * @return \DateTime|null
     */
    public function getOnetimeExecutionDate(): ?\DateTime
    {
        return $this->onetimeExecutionDate;
    }

    /**
     * @return int
     */
    public function getMinutesStreamDuration(): int
    {
        return $this->minutesStreamDuration;
    }

    /**
     * @return bool
     */
    public function isRunning(): bool
    {
        return $this->isRunning;
    }

    /**
     * @return bool
     */
    public function isRecurring(): bool
    {
        return $this->isRecurring;
    }

    /**
     * @return \DateTime
     */
    public function getNextExecutionTime(): \DateTime
    {
        return $this->nextExecutionTime;
    }

    /**
     * @return array
     */
    public function getPayload()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'executionDay' => $this->getExecutionDay(),
            'executionTime' => $this->getExecutionTime(),
            'onetimeExecutionDate' => $this->getOnetimeExecutionDate(),
            'minutesStreamDuration' => $this->getMinutesStreamDuration(),
            'isRunning' => $this->isRunning(),
            'isRecurring' => $this->isRecurring(),
            'nextExecutionTime' => $this->getNextExecutionTime(),
        ];
    }

    /**
     * @throws \InvalidArgumentException
     * @param StreamSchedule $streamSchedule
     */
    private static function validate(StreamSchedule $streamSchedule)
    {
        Assert::notNull($streamSchedule->getId(), 'expected id, got null');
        Assert::notNull($streamSchedule->getName(), 'expected name, got null');
        Assert::notNull($streamSchedule->getStreamDuration(), 'expected streamDuration, got null');
        Assert::notNull($streamSchedule->isRunning(), 'expected running bool, got null');
    }
}
