<?php

namespace App\Grid\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterContain;
use App\Common\Data\Operator\FilterEqual;
use App\Common\Data\Operator\FilterNotContain;
use App\Common\Data\Operator\FilterNotEqual;
use App\Common\Data\Operator\SortAscending;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\FilterType;
use App\Common\Form\Type\PaginationType;
use App\Common\Form\Type\SortType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['code', 'size', 'sellingPrice', 'minimumStock', 'isInactive', 'name'],
                'field_operators_list' => [
                    'code' => [FilterContain::class, FilterNotContain::class],
                    'size' => [FilterContain::class, FilterNotContain::class],
                    'sellingPrice' => [FilterEqual::class, FilterNotEqual::class],
                    'minimumStock' => [FilterEqual::class, FilterNotEqual::class],
                    'isInactive' => [FilterEqual::class, FilterNotEqual::class],
                    'name' => [FilterContain::class, FilterNotContain::class],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['code', 'size', 'sellingPrice', 'minimumStock', 'isInactive', 'name'],
                'field_operators_list' => [
                    'code' => [SortAscending::class, SortDescending::class],
                    'size' => [SortAscending::class, SortDescending::class],
                    'sellingPrice' => [SortAscending::class, SortDescending::class],
                    'minimumStock' => [SortAscending::class, SortDescending::class],
                    'isInactive' => [SortAscending::class, SortDescending::class],
                    'name' => [SortAscending::class, SortDescending::class],
                ],
            ])
            ->add('pagination', PaginationType::class, ['size_choices' => [10, 20, 50, 100]])
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
