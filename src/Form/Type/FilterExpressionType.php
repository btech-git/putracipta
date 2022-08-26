<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterExpressionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $operatorClassNames = array_keys($options['operator_options']);

        $operators = array_map(fn(string $className): string => $className, $operatorClassNames);
        $choices = array_combine(array_values($operators), array_values($operators));
        $builder->add(0, ChoiceType::class, ['choices' => $choices, 'required' => false]);

        $maxValueCount = max(array_map(fn(string $className): int => (new $className())->getValueCount(), $operatorClassNames));
        foreach ($options['operator_options'] as $operatorClassName => $valueOption) {
            for ($i = 0; $i < $maxValueCount; $i++) {
                $builder->add($i + 1, TextType::class, ['required' => false, 'empty_data' => '']);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'operator_options' => [],
        ]);
        $resolver->setAllowedTypes('operator_options', ['array']);
    }
}
