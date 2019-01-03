<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Configuration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'cameraConfiguration',
                CollectionType::class,
                [
                    'entry_type' => CameraConfigurationType::class,
                    'entry_options' => array('label' => false),
                ]
            )
            ->add(
                'submitButton',
                SubmitType::class,
                [
                    'label' => 'camera_configuration_list.submit_button',
                    'translation_domain' => 'camera_configuration',
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
            'data_class' => Configuration::class,
        ));
    }
}
