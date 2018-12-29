<?php
declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocaleType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => [
                'user.form.local_type.dutch' => 'nl',
                'user.form.local_type.english' => 'en',
            ],
            'label' => 'user.form.locale_type.label_locale',
            'translation_domain' => 'users',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
