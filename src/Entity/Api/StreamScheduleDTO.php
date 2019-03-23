<?php
declare(strict_types=1);

namespace App\Entity\Api;

use App\Entity\StreamSchedule;
use App\Entity\Weekday;
use App\Exception\StreamSchedule\CouldNotCreateStreamScheduleDTOException;
use Webmozart\Assert\Assert;
use Swagger\Annotations as SWG;

class StreamScheduleDTO
{
    /**
     * @var string
     * @SWG\Property(type="string", maxLength=36, description="Uuid, the unique identifier stream schedule.")
     */
    private $id;

    /**
     * @var string
     * @SWG\Property(type="string", maxLength=50)
     */
    private $name;

    /**
     * @var string|null
     * @SWG\Property(type="string", maxLength=25, description="null if onetime schedule, day of the week if recurring")
     */
    private $executionDay;

    /**
     * @var string|null
     * @SWG\Property(type="string", maxLength=25, description="null if onetime schedule, time of the day if recurring")
     */
    private $executionTime;

    /**
     * @var \DateTime|null
     * @SWG\Property(type="DateTime", description="null if recurring schedule, DateTime if one-time schedule")
     */
    private $onetimeExecutionDate;

    /**
     * @var int
     * @SWG\Property(type="int", maxLength=10, description="total stream duration in minutes")
     */
    private $minutesStreamDuration;

    /**
     * @var bool
     * @SWG\Property(type="bool")
     */
    private $isRunning;

    /**
     * @var bool
     * @SWG\Property(type="bool" description"Boolean if stream is a recurring or one-time schedule")
     */
    private $isRecurring;

    /**
     * @var \DateTime
     * @SWG\Property(type="DateTime", description="next planned execution from the stream")
     */
    private $nextExecutionTime;

    /**
     * @throws CouldNotCreateStreamScheduleDTOException
     * @param StreamSchedule $streamSchedule
     */
    private function __construct(StreamSchedule $streamSchedule)
    {
        try {
            self::validate($streamSchedule);
            $this->executionDay = $streamSchedule->getExecutionDay() ?
                    Weekday::getDayOfTheWeekById($streamSchedule->getExecutionDay()) : null;
            $this->nextExecutionTime = $streamSchedule->getNextExecutionTime();
        } catch (\InvalidArgumentException $exception) {
            throw CouldNotCreateStreamScheduleDTOException::invalidArguments($streamSchedule, $exception);
        }
        $this->id = $streamSchedule->getId();
        $this->name = $streamSchedule->getName();
        $this->executionTime =
            $streamSchedule->getExecutionTime()     ? $streamSchedule->getExecutionTime()->format('H:i:s') : null;
        $this->onetimeExecutionDate = $streamSchedule->getOnetimeExecutionDate();
        $this->minutesStreamDuration = $streamSchedule->getStreamDuration();
        $this->isRunning = $streamSchedule->isRunning();
        $this->isRecurring = $streamSchedule->isRecurring();
    }

    /**
     * @param StreamSchedule $streamSchedule
     * @return StreamScheduleDTO
     */
    public static function createFromStreamSchedule(StreamSchedule $streamSchedule)
    {
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
    public function grabPayload()
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
