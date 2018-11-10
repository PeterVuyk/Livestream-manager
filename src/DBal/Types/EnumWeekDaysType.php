<?php
declare(strict_types=1);

namespace App\DBal\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class EnumWeekDaysType extends Type
{
    const ENUM_WEEK_DAYS = 'enumWeekDays';
    const ENUM_MONDAY = 'monday';
    const ENUM_TUESDAY = 'tuesday';
    const ENUM_WEDNESDAY = 'wednesday';
    const ENUM_THURSDAY = 'thursday';
    const ENUM_FRIDAY = 'friday';
    const ENUM_SATURDAY = 'saturday';
    const ENUM_SUNDAY = 'sunday';

    const DAYS_OF_WEEK = [
        self::ENUM_MONDAY,
        self::ENUM_TUESDAY,
        self::ENUM_WEDNESDAY,
        self::ENUM_THURSDAY,
        self::ENUM_FRIDAY,
        self::ENUM_SATURDAY,
        self::ENUM_SUNDAY
    ];

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return "ENUM('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!in_array($value, self::DAYS_OF_WEEK)) {
            throw new \InvalidArgumentException("Invalid day of the week");
        }
        return $value;
    }

    public function getName()
    {
        return self::ENUM_WEEK_DAYS;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
