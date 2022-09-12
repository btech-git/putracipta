<?php

namespace App\Form\AccessPoint;

use App\Entity\AccessPoint\ItemBoxKey;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemBoxKeyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('abc')
            ->add('def')
            ->add('ghi')
            ->add('jkl')
            ->add('mno')
            ->add('pqrs')
            ->add('tuv')
            ->add('wxyz')
            ->add('qwerty')
            ->add('dvorak')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ItemBoxKey::class,
        ]);
    }
}
