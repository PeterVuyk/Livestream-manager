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
                'label' => 'recurring.form.label.detail.name',
                'required' => true,
                'translation_domain' => 'schedule_create',
                'attr' => ['class' => 'form-control', 'placeholder' => 'name'],
            ]
        );

        $builder->add(
            'command',
            CommandChoiceType::class,
            [
                'label' => 'recurring.form.label.detail.command',
                'translation_domain' => 'schedule_create',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ]
        );

        $builder->add(
            'executionDay',
            ChoiceType::class,
            [
                'choices' => $this->getDaysOfTheWeek(),
                'label' => 'recurring.form.label.detail.label.weekday_choice',
                'translation_domain' => 'schedule_create',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ]
        );

        $builder->add(
            'executionTime',
            TimeType::class,
            [
                'input' => 'datetime',
                'label' => 'recurring.form.label.detail.label.time',
                'translation_domain' => 'schedule_create',
                'required' => true,
            ]
        );

        $builder->add(
            'priority',
            IntegerType::class,
            [
                'label' => 'recurring.form.label.detail.priority',
                'translation_domain' => 'schedule_create',
                'empty_data' => 0,
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ]
        );

        $builder->add(
            'runWithNextExecution',
            CheckboxType::class,
            [
                'label' => 'recurring.form.label.detail.run_with_next_execution',
                'required' => false,
                'translation_domain' => 'schedule_create',
            ]
        );

        $builder->add(
            'disabled',
            CheckboxType::class,
            [
                'label' => 'recurring.form.label.detail.disabled',
                'translation_domain' => 'schedule_create',
                'required' => false,
            ]
        );

        $builder->add(
            'save',
            SubmitType::class,
            [
                'label' => 'recurring.form.label.detail.save',
                'translation_domain' => 'schedule_create',
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
            'recurring.form.weekday_choice_type.monday' => EnumWeekDaysType::ENUM_MONDAY,
            'recurring.form.weekday_choice_type.tuesday' => EnumWeekDaysType::ENUM_TUESDAY,
            'recurring.form.weekday_choice_type.wednesday' => EnumWeekDaysType::ENUM_WEDNESDAY,
            'recurring.form.weekday_choice_type.thursday' => EnumWeekDaysType::ENUM_THURSDAY,
            'recurring.form.weekday_choice_type.friday' => EnumWeekDaysType::ENUM_FRIDAY,
            'recurring.form.weekday_choice_type.saturday' => EnumWeekDaysType::ENUM_SATURDAY,
            'recurring.form.weekday_choice_type.sunday' => EnumWeekDaysType::ENUM_SUNDAY,
        ];
    }
}
