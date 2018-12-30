<?php
declare(strict_types=1);

namespace App\Twig;

use App\Entity\Weekday;
use Twig\Extension\AbstractExtension;

class WeekdayExtension extends AbstractExtension
{
    const GET_WEEKDAY = 'getWeekday';

    private $weekdays = [
        Weekday::MONDAY => 'schedule_list.weekday.monday',
        Weekday::TUESDAY => 'schedule_list.weekday.tuesday',
        Weekday::WEDNESDAY => 'schedule_list.weekday.wednesday',
        Weekday::THURSDAY => 'schedule_list.weekday.thursday',
        Weekday::FRIDAY => 'schedule_list.weekday.friday',
        Weekday::SATURDAY => 'schedule_list.weekday.saturday',
        Weekday::SUNDAY => 'schedule_list.weekday.sunday',
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
        if (!Weekday::validate($id)) {
            return '-';
        }
        return $this->weekdays[$id];
    }
}
