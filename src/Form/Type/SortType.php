<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = ['Ascending' => 'ASC', 'Descending' => 'DESC'];
        foreach ($options['field_names'] as $fieldName) {
            $builder->add($fieldName, ChoiceType::class, ['choices' => $choices, 'required' => false]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'field_names' => [],
        ]);
        $resolver->setAllowedTypes('field_names', ['array']);
    }
}
