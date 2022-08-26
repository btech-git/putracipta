<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaginationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('number', TextType::class, ['required' => false]);
        if ($options['size_choices'] === null) {
            $builder->add('size', TextType::class);
        } else {
            $builder->add('size', ChoiceType::class, ['choices' => $options['size_choices'], 'choice_label' => fn($choice, $key, $value) => $value]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'size_choices' => null,
        ]);
        $resolver->setAllowedTypes('size_choices', ['null', 'array']);
    }
}
