<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Weekdays;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WeekdayType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['choices' => [
            'stream.form.weekday_choice_type.monday' => Weekdays::MONDAY,
            'stream.form.weekday_choice_type.tuesday' => Weekdays::TUESDAY,
            'stream.form.weekday_choice_type.wednesday' => Weekdays::WEDNESDAY,
            'stream.form.weekday_choice_type.thursday' => Weekdays::THURSDAY,
            'stream.form.weekday_choice_type.friday' => Weekdays::FRIDAY,
            'stream.form.weekday_choice_type.saturday' => Weekdays::SATURDAY,
            'stream.form.weekday_choice_type.sunday' => Weekdays::SUNDAY,
        ]]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
