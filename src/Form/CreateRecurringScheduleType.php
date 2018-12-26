<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\StreamSchedule;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CreateRecurringScheduleType extends AbstractType
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
        $builder->add('isRunning', HiddenType::class, ['empty_data' => false]);

        $builder->add(
            'name',
            TextType::class,
            [
                'label' => 'stream.form.label.detail.name',
                'required' => true,
                'translation_domain' => 'schedule_create',
                'attr' => ['class' => 'form-control', 'placeholder' => 'name'],
            ]
        );

        $builder->add(
            'executionDay',
            WeekdayType::class,
            [
                'label' => 'stream.form.label.detail.label.weekday_choice',
                'translation_domain' => 'schedule_create',
                'required' => true,
                'placeholder' => 'stream.form.placeholder.weekday_choice',
                'attr' => ['class' => 'form-control'],
            ]
        );

        $builder->add(
            'executionTime',
            TimeType::class,
            [
                'input' => 'datetime',
                'label' => 'stream.form.label.detail.label.time',
                'translation_domain' => 'schedule_create',
                'data' => new \DateTime(),
                'required' => true,
            ]
        );

        $builder->add(
            'streamDuration',
            IntegerType::class,
            [
                'label' => 'stream.form.label.detail.minutes_to_run',
                'translation_domain' => 'schedule_create',
                'data' => 90,
                'attr' => ['class' => 'form-control', 'min' => 1],
            ]
        );

        $builder->add(
            'disabled',
            CheckboxType::class,
            [
                'label' => 'stream.form.label.detail.disabled',
                'translation_domain' => 'schedule_create',
                'required' => false,
            ]
        );

        $builder->add(
            'save',
            SubmitType::class,
            [
                'label' => 'stream.form.label.detail.save',
                'translation_domain' => 'schedule_create',
                'attr' => ['class' => 'btn btn-primary pull-right'],
            ]
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => StreamSchedule::class,
            'wrapper_attr' => 'default_wrapper',
        ));
    }
}
