<?php
declare(strict_types=1);

namespace App\Entity;

use App\Exception\InvalidWeekdayException;

class Weekday
{
    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;
    const SUNDAY = 7;

    /**
     * @return array
     */
    public static function getDaysOfTheWeek(): array
    {
        return [
            self::MONDAY => 'monday',
            self::TUESDAY => 'tuesday',
            self::WEDNESDAY => 'wednesday',
            self::THURSDAY => 'thursday',
            self::FRIDAY => 'friday',
            self::SATURDAY => 'saturday',
            self::SUNDAY => 'sunday',
        ];
    }

    /**
     * @return array
     */
    public static function getDaysOfTheWeekKeys(): array
    {
        return array_keys(self::getDaysOfTheWeek());
    }

    /**
     * @param int $day
     * @return bool
     */
    public static function validate(int $day): bool
    {
        if (in_array($day, self::getDaysOfTheWeekKeys())) {
            return true;
        }
        return false;
    }

    /**
     * @param int $day
     * @return string
     */
    public static function getDayOfTheWeekById(int $day): string
    {
        if (!self::validate($day)) {
            throw InvalidWeekdayException::invalidDayInput($day);
        }
        return self::getDaysOfTheWeek()[$day];
    }
}
