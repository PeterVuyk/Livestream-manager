<?php
declare(strict_types=1);

namespace App\Twig;

use App\Entity\Weekdays;
use Twig\Extension\AbstractExtension;

class Weekday extends AbstractExtension
{
    const GET_WEEKDAY = 'getWeekday';

    private $weekdays = [
        Weekdays::MONDAY => 'schedule_list.weekday.monday',
        Weekdays::TUESDAY => 'schedule_list.weekday.tuesday',
        Weekdays::WEDNESDAY => 'schedule_list.weekday.wednesday',
        Weekdays::THURSDAY => 'schedule_list.weekday.thursday',
        Weekdays::FRIDAY => 'schedule_list.weekday.friday',
        Weekdays::SATURDAY => 'schedule_list.weekday.saturday',
        Weekdays::SUNDAY => 'schedule_list.weekday.sunday',
    ];

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(self::GET_WEEKDAY, [$this, self::GET_WEEKDAY], ['is_safe' => ['html']])
        ];
    }

    public function getWeekday(int $id)
    {
        if (!Weekdays::validate($id)) {
            return '-';
        }
        return $this->weekdays[$id];
    }
}
