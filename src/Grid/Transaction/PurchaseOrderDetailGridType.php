<?php

namespace App\Grid\Transaction;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterEqual;
use App\Common\Data\Operator\FilterNotEqual;
use App\Common\Data\Operator\SortAscending;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\FilterType;
use App\Common\Form\Type\PaginationType;
use App\Common\Form\Type\SortType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseOrderDetailGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['quantity', 'unitPrice', 'discount', 'isCanceled'],
                'field_operators_list' => [
                    'quantity' => [FilterEqual::class, FilterNotEqual::class],
                    'unitPrice' => [FilterEqual::class, FilterNotEqual::class],
                    'discount' => [FilterEqual::class, FilterNotEqual::class],
                    'isCanceled' => [FilterEqual::class, FilterNotEqual::class],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['quantity', 'unitPrice', 'discount', 'isCanceled'],
                'field_operators_list' => [
                    'quantity' => [SortAscending::class, SortDescending::class],
                    'unitPrice' => [SortAscending::class, SortDescending::class],
                    'discount' => [SortAscending::class, SortDescending::class],
                    'isCanceled' => [SortAscending::class, SortDescending::class],
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
