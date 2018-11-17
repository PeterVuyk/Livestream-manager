<?php
declare(strict_types=1);

namespace App\Entity;

class Weekdays
{
    const ENUM_WEEK_DAYS = 'enumWeekDays';
    const ENUM_MONDAY = 'monday';
    const ENUM_TUESDAY = 'tuesday';
    const ENUM_WEDNESDAY = 'wednesday';
    const ENUM_THURSDAY = 'thursday';
    const ENUM_FRIDAY = 'friday';
    const ENUM_SATURDAY = 'saturday';
    const ENUM_SUNDAY = 'sunday';

    public static function getDaysOfTheWeek()
    {
        return [
            self::ENUM_MONDAY,
            self::ENUM_TUESDAY,
            self::ENUM_WEDNESDAY,
            self::ENUM_THURSDAY,
            self::ENUM_FRIDAY,
            self::ENUM_SATURDAY,
            self::ENUM_SUNDAY
        ];
    }
}
