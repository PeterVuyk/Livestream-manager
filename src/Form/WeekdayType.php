<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Weekday;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WeekdayType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['choices' => [
            'stream.form.weekday_choice_type.monday' => Weekday::MONDAY,
            'stream.form.weekday_choice_type.tuesday' => Weekday::TUESDAY,
            'stream.form.weekday_choice_type.wednesday' => Weekday::WEDNESDAY,
            'stream.form.weekday_choice_type.thursday' => Weekday::THURSDAY,
            'stream.form.weekday_choice_type.friday' => Weekday::FRIDAY,
            'stream.form.weekday_choice_type.saturday' => Weekday::SATURDAY,
            'stream.form.weekday_choice_type.sunday' => Weekday::SUNDAY,
        ]]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
