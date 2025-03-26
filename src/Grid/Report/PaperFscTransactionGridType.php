<?php

namespace App\Grid\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Common\Data\Operator\FilterContain;
use App\Common\Data\Operator\FilterEqual;
use App\Common\Data\Operator\FilterNotBetween;
use App\Common\Data\Operator\FilterNotContain;
use App\Common\Data\Operator\FilterNotEqual;
use App\Common\Data\Operator\SortAscending;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\FilterType;
use App\Common\Form\Type\PaginationType;
use App\Common\Form\Type\SortType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaperFscTransactionGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => [
                    'purchaseOrderPaperHeader:transactionDate', 
                    'name', 
                    'code', 
                    'type', 
                    'weight',
                    'materialSubCategory:code', 
                ],
                'field_label_list' => [
                    'purchaseOrderPaperHeader:transactionDate' => 'Tanggal',
                    'materialSubCategory:code' => 'Category', 
                    'name' => 'Paper',
                ],
                'field_operators_list' => [
                    'purchaseOrderPaperHeader:transactionDate' => [FilterBetween::class, FilterNotBetween::class],
                    'materialSubCategory:code' => [FilterContain::class, FilterNotContain::class],
                    'code' => [FilterEqual::class, FilterNotEqual::class],
                    'name' => [FilterContain::class, FilterNotContain::class],
                    'type' => [FilterEqual::class, FilterNotEqual::class],
                    'weight' => [FilterEqual::class, FilterNotEqual::class],
                ],
                'field_value_type_list' => [
                    'type' => ChoiceType::class,
                ],
                'field_value_options_list' => [
                    'purchaseOrderPaperHeader:transactionDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                    'type' => ['choices' => ['000' => 'non', 'FSC' => 'fsc']],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => [
                    'purchaseOrderPaperHeader:transactionDate', 
                    'name', 
                    'code', 
                    'type', 
                    'weight',
                    'materialSubCategory:code', 
                ],
                'field_label_list' => [
                    'purchaseOrderPaperHeader:transactionDate' => 'Tanggal',
                    'materialSubCategory:code' => 'Category', 
                    'name' => 'Paper',
                ],
                'field_operators_list' => [
                    'purchaseOrderPaperHeader:transactionDate' => [SortAscending::class, SortDescending::class],
                    'materialSubCategory:code' => [SortAscending::class, SortDescending::class],
                    'name' => [SortAscending::class, SortDescending::class],
                    'code' => [SortAscending::class, SortDescending::class],
                    'type' => [SortAscending::class, SortDescending::class],
                    'weight' => [SortAscending::class, SortDescending::class],
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
