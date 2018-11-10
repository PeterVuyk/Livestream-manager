<?php
declare(strict_types=1);

namespace App\Form;

use App\DBal\Types\EnumWeekDaysType;
use App\Entity\RecurringSchedule;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RecurringScheduleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @throws \Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', HiddenType::class, ['empty_data' => Uuid::uuid4()]);
        $builder->add('wrecked', HiddenType::class, ['empty_data' => false]);

        $builder->add(
            'name',
            TextType::class,
            [
                'label' => 'recurring_scheduler.detail.name',
                'required' => true,
                'attr' => ['class' => 'form-control', 'placeholder' => 'name'],
            ]
        );

        $builder->add(
            'command',
            CommandChoiceType::class,
            [
                'label' => 'recurring_scheduler.detail.command',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ]
        );

        $builder->add(
            'executionDay',
            ChoiceType::class,
            [
                'choices' => $this->getDaysOfTheWeek(),
                'label' => 'recurring_scheduler.detail.label.weekday_choice',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ]
        );

        $builder->add(
            'executionTime',
            TimeType::class,
            [
                'input' => 'datetime',
                'label' => 'recurring_scheduler.detail.label.time',
                'required' => true,
            ]
        );

        $builder->add(
            'priority',
            IntegerType::class,
            [
                'label' => 'recurring_scheduler.detail.priority',
                'empty_data' => 0,
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ]
        );

        $builder->add(
            'runWithNextExecution',
            CheckboxType::class,
            [
                'label' => 'recurring_scheduler.detail.run_with_next_execution',
                'required' => false,
            ]
        );

        $builder->add(
            'disabled',
            CheckboxType::class,
            [
                'label' => 'recurring_scheduler.detail.disabled',
                'required' => false,
            ]
        );

        $builder->add(
            'save',
            SubmitType::class,
            [
                'label' => 'recurring_scheduler.detail.save',
                'attr' => ['class' => 'btn btn-success btn-lg pull-right'],
            ]
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => RecurringSchedule::class,
            'wrapper_attr' => 'default_wrapper',
        ));
    }

    private function getDaysOfTheWeek(): array
    {
        return [
            'monday' => EnumWeekDaysType::ENUM_MONDAY,
            'tuesday' => EnumWeekDaysType::ENUM_TUESDAY,
            'wednesday' => EnumWeekDaysType::ENUM_WEDNESDAY,
            'thursday' => EnumWeekDaysType::ENUM_THURSDAY,
            'friday' => EnumWeekDaysType::ENUM_FRIDAY,
            'saturday' => EnumWeekDaysType::ENUM_SATURDAY,
            'sunday' => EnumWeekDaysType::ENUM_SUNDAY,
        ];
    }
}
