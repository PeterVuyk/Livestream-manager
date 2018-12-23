<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'registration_form.label_email',
                    'translation_domain' => 'users',
                ]
            )
            ->add(
                'username',
                TextType::class,
                [
                    'label' => 'registration_form.label_username',
                    'translation_domain' => 'users',
                ]
            )
            ->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'first_options'  => ['label' => 'registration_form.label_password'],
                    'second_options' => ['label' => 'registration_form.label_repeat_password'],
                    'translation_domain' => 'users',
                    'required' => true,
                    'constraints' => [
                        new Assert\NotBlank(),
                    ]
                ])
            ->add(
                'submitButton',
                SubmitType::class,
                [
                    'label' => 'registration_form.submit_button',
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
            'data_class' => User::class,
        ));
    }
}
