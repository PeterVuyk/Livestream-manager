<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Channel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CreateChannelType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'channel_create.label_channel',
                    'translation_domain' => 'channels',
                ]
            )

            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'user_details.submit_button',
                    'translation_domain' => 'users',
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
            'data_class' => Channel::class,
        ));
    }
}
