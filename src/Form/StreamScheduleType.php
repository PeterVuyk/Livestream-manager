<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\StreamSchedule;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class StreamScheduleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @throws \Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', HiddenType::class, ['empty_data' => Uuid::uuid4()]);

        $builder->add(
            'name',
            TextType::class,
            [
                'label' => 'recurring_scheduler.detail.name',
                'required' => true,
            ]
        );

        $builder->add(
            'command',
            CommandChoiceType::class,
            [
                'label' => 'recurring_scheduler.detail.command',
                'required' => true,
            ]
        );

        $builder->add(
            'cronExpression',
            TextType::class,
            [
                'label' => 'recurring_scheduler.detail.cronExpression',
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
