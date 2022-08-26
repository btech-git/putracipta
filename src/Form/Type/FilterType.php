<?php

namespace App\Form\Type;

use App\Form\Type\Operator\FilterOperators;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($options['field_options'] as $fieldName => $operatorListOptions) {
            $operatorOptions = is_array($operatorListOptions) ? $operatorListOptions : (new $operatorListOptions())->getOperatorList();
            $builder->add($fieldName, FilterExpressionType::class, ['operator_options' => $operatorOptions]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'field_options' => [],
        ]);
        $resolver->setAllowedTypes('field_options', ['array', FilterOperators::class]);
    }
}
