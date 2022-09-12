<?php

namespace App\Grid;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Common\Data\Operator\FilterEqual;
use App\Common\Data\Operator\FilterNotEqual;
use App\Common\Data\Operator\SortAscending;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\FilterType;
use App\Common\Form\Type\PaginationType;
use App\Common\Form\Type\SortType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SampleGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['name', 'price'],
                'field_operators_list' => [
                    'name' => [FilterEqual::class, FilterNotEqual::class, FilterBetween::class],
                    'price' => [FilterEqual::class, FilterNotEqual::class, FilterBetween::class],
                ],
                'field_value_type_list' => [
                    'name' => TextType::class,
                    'price' => TextType::class,
                ],
                'field_value_options_list' => [
                    'name' => ['required' => false, 'empty_data' => ''],
                    'price' => ['required' => false, 'empty_data' => ''],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['name', 'price'],
                'field_operators_list' => [
                    'name' => [SortAscending::class, SortDescending::class],
                    'price' => [SortAscending::class, SortDescending::class],
                ],
            ])
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
