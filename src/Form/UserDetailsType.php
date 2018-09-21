<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserDetailsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $action = $options['action'];

        $builder
            ->add('email', EmailType::class, ['label' => 'user_details.label_email'])
            ->add('username', TextType::class, ['label' => 'user_details.label_username'])
            ->add('active', CheckBoxType::class, [
                'label' => 'user_details.label_is_active',
                'required' => false,
            ])
            ->add(
                'submitButton',
                SubmitType::class, [
                    'label' => 'user_details.submit_button',
                    'attr' => ['class' => 'btn btn-secondary'],
                ]
            );
        $builder->setAction($action);
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
