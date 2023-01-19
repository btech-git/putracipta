<?php

namespace App\Grid\Stock;

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

class MaterialRequestHeaderGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['totalQuantity', 'departmentName', 'workOrderNumber', 'partNumber', 'pickupDate', 'isCanceled', 'codeNumberOrdinal', 'codeNumberMonth', 'codeNumberYear', 'createdTransactionDateTime', 'modifiedTransactionDateTime', 'transactionDate', 'note'],
                'field_operators_list' => [
                    'totalQuantity' => [FilterEqual::class, FilterNotEqual::class],
                    'departmentName' => [FilterContain::class, FilterNotContain::class],
                    'workOrderNumber' => [FilterContain::class, FilterNotContain::class],
                    'partNumber' => [FilterContain::class, FilterNotContain::class],
                    'pickupDate' => [FilterEqual::class, FilterNotEqual::class],
                    'isCanceled' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'createdTransactionDateTime' => [FilterEqual::class, FilterNotEqual::class],
                    'modifiedTransactionDateTime' => [FilterEqual::class, FilterNotEqual::class],
                    'transactionDate' => [FilterEqual::class, FilterNotEqual::class],
                    'note' => [FilterContain::class, FilterNotContain::class],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['totalQuantity', 'departmentName', 'workOrderNumber', 'partNumber', 'pickupDate', 'isCanceled', 'codeNumberOrdinal', 'codeNumberMonth', 'codeNumberYear', 'createdTransactionDateTime', 'modifiedTransactionDateTime', 'transactionDate', 'note'],
                'field_operators_list' => [
                    'totalQuantity' => [SortAscending::class, SortDescending::class],
                    'departmentName' => [SortAscending::class, SortDescending::class],
                    'workOrderNumber' => [SortAscending::class, SortDescending::class],
                    'partNumber' => [SortAscending::class, SortDescending::class],
                    'pickupDate' => [SortAscending::class, SortDescending::class],
                    'isCanceled' => [SortAscending::class, SortDescending::class],
                    'codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'createdTransactionDateTime' => [SortAscending::class, SortDescending::class],
                    'modifiedTransactionDateTime' => [SortAscending::class, SortDescending::class],
                    'transactionDate' => [SortAscending::class, SortDescending::class],
                    'note' => [SortAscending::class, SortDescending::class],
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
