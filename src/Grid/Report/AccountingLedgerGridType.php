<?php

namespace App\Grid\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Common\Data\Operator\FilterContain;
use App\Common\Data\Operator\FilterNotBetween;
use App\Common\Data\Operator\FilterNotContain;
use App\Common\Data\Operator\SortAscending;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\FilterType;
use App\Common\Form\Type\PaginationType;
use App\Common\Form\Type\SortType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountingLedgerGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['code', 'name', 'accountingLedger:transactionDate'],
                'field_operators_list' => [
                    'code' => [FilterContain::class, FilterNotContain::class],
                    'name' => [FilterContain::class, FilterNotContain::class],
                    'accountingLedger:transactionDate' => [FilterBetween::class, FilterNotBetween::class],
                ],
                'field_value_options_list' => [
                    'accountingLedger:transactionDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['code', 'name', 'accountingLedger:transactionDate'],
                'field_operators_list' => [
                    'code' => [SortAscending::class, SortDescending::class],
                    'name' => [SortAscending::class, SortDescending::class],
                    'accountingLedger:transactionDate' => [SortAscending::class, SortDescending::class],
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
