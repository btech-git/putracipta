<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterComparisonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $operators = ['EQ', 'NEQ', 'LT', 'LTE', 'GT', 'GTE', 'CT', 'NCT', 'SW', 'NSW', 'EW', 'NEW'];
        $choices = array_combine(array_values($operators), array_values($operators));
        $builder->add('operator', ChoiceType::class, ['choices' => $choices, 'required' => false]);
        $builder->add('value', TextType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'field_names' => [],
        ]);
        $resolver->setAllowedTypes('field_names', ['array']);
    }
}
