<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterConnectiveType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => [
                'OR' => 'OR',
                'AND' => 'AND',
            ],
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
