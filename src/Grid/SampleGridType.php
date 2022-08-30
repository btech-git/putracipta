<?php

namespace App\Grid;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterStringOperators;
use App\Common\Form\Type\FilterType;
use App\Common\Form\Type\PaginationType;
use App\Common\Form\Type\SortType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SampleGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, ['field_options' => [
                'name' => FilterStringOperators::class,
                'price' => FilterStringOperators::class,
            ]])
            ->add('sort', SortType::class, ['field_names' => ['name', 'price']])
            ->add('pagination', PaginationType::class, ['size_choices' => [2, 10, 20, 50, 100]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DataCriteria::class,
            'csrf_protection' => false,
        ]);
    }
}
