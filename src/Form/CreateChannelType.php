<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Channel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

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
                'channelName',
                TextType::class,
                [
                    'label' => 'channel_create.label_channel',
                    'translation_domain' => 'channels',
                    'constraints' => [
                        new Assert\Regex([
                            'pattern' => '/^[^\s-]+$/i',
                            'htmlPattern' => '^[^\s-]+$',
                            'message' => 'Space in channel name not allowed',
                        ]),
                    ],
                ]
            )
            ->add(
                'username',
                TextType::class,
                [
                    'label' => 'channel_create.label_username',
                    'translation_domain' => 'channels',
                ]
            )
            ->add(
                'host',
                TextType::class,
                [
                    'label' => 'channel_create.label_host',
                    'translation_domain' => 'channels',
                ]
            );
        if ($options['data'] instanceof Channel && empty($options['data']->getSecret())) {
            $builder
                ->add(
                    'secret',
                    RepeatedType::class,
                    [
                        'type' => PasswordType::class,
                        'first_options'  => ['label' => 'channel_create.label_secret'],
                        'second_options' => ['label' => 'channel_create.label_repeat_password'],
                        'translation_domain' => 'channels',
                        'required' => true,
                        'constraints' => [
                            new Assert\NotBlank(),
                        ]
                    ]
                );
        }
        $builder->add(
            'save',
            SubmitType::class,
            [
                'label' => 'channel_create.submit_button',
                'translation_domain' => 'channels',
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
