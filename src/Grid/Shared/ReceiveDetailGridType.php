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
use App\Entity\PurchaseHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReceiveDetailGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => [
                    'transactionDate', 
                    'material:name', 
                    'material:code', 
                    'paper:name', 
                    'paper:code', 
                    'purchaseOrderHeader:codeNumberOrdinal', 
                    'purchaseOrderHeader:codeNumberMonth', 
                    'purchaseOrderHeader:codeNumberYear', 
                    'purchaseOrderPaperHeader:codeNumberOrdinal', 
                    'purchaseOrderPaperHeader:codeNumberMonth', 
                    'purchaseOrderPaperHeader:codeNumberYear', 
                    'receiveHeader:supplierDeliveryCodeNumber'
                ],
                'field_label_list' => [
                    'material:name' => 'Material',
                    'material:code' => 'Code',
                    'paper:name' => 'Paper',
                    'paper:code' => 'Code',
                    'receiveHeader:supplierDeliveryCodeNumber' => 'SJ #',
                ],
                'field_operators_list' => [
                    'transactionDate' => [FilterEqual::class, FilterNotEqual::class],
                    'material:name' => [FilterContain::class, FilterNotContain::class],
                    'material:code' => [FilterContain::class, FilterNotContain::class],
                    'paper:name' => [FilterContain::class, FilterNotContain::class],
                    'paper:code' => [FilterContain::class, FilterNotContain::class],
                    'purchaseOrderHeader:codeNumberOrdinal' => [FilterContain::class, FilterNotContain::class],
                    'purchaseOrderHeader:codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'purchaseOrderHeader:codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'purchaseOrderPaperHeader:codeNumberOrdinal' => [FilterContain::class, FilterNotContain::class],
                    'purchaseOrderPaperHeader:codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'purchaseOrderPaperHeader:codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'receiveHeader:supplierDeliveryCodeNumber' => [FilterContain::class, FilterNotContain::class],
                ],
                'field_value_type_list' => [
                    'purchaseOrderHeader:codeNumberOrdinal' => IntegerType::class,
                    'purchaseOrderHeader:codeNumberMonth' => ChoiceType::class,
                    'purchaseOrderHeader:codeNumberYear' => IntegerType::class,
                    'purchaseOrderPaperHeader:codeNumberOrdinal' => IntegerType::class,
                    'purchaseOrderPaperHeader:codeNumberMonth' => ChoiceType::class,
                    'purchaseOrderPaperHeader:codeNumberYear' => IntegerType::class,
                ],
                'field_value_options_list' => [
                    'purchaseOrderHeader:codeNumberMonth' => ['choices' => array_flip(PurchaseHeader::MONTH_ROMAN_NUMERALS)],
                    'purchaseOrderPaperHeader:codeNumberMonth' => ['choices' => array_flip(PurchaseHeader::MONTH_ROMAN_NUMERALS)],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => [
                    'transactionDate', 
                    'material:name', 
                    'material:code', 
                    'paper:name', 
                    'paper:code', 
                    'purchaseOrderHeader:codeNumberOrdinal', 
                    'purchaseOrderHeader:codeNumberMonth', 
                    'purchaseOrderHeader:codeNumberYear', 
                    'purchaseOrderPaperHeader:codeNumberOrdinal', 
                    'purchaseOrderPaperHeader:codeNumberMonth', 
                    'purchaseOrderPaperHeader:codeNumberYear', 
                    'receiveHeader:supplierDeliveryCodeNumber'
                ],
                'field_label_list' => [
                    'material:name' => 'Material',
                    'material:code' => 'Code',
                    'paper:name' => 'Paper',
                    'paper:code' => 'Code',
                    'receiveHeader:supplierDeliveryCodeNumber' => 'SJ #',
                ],
                'field_operators_list' => [
                    'transactionDate' => [SortAscending::class, SortDescending::class],
                    'material:name' => [SortAscending::class, SortDescending::class],
                    'material:code' => [SortAscending::class, SortDescending::class],
                    'paper:name' => [SortAscending::class, SortDescending::class],
                    'paper:code' => [SortAscending::class, SortDescending::class],
                    'purchaseOrderHeader:codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'purchaseOrderHeader:codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'purchaseOrderHeader:codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'purchaseOrderPaperHeader:codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'purchaseOrderPaperHeader:codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'purchaseOrderPaperHeader:codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'receiveHeader:supplierDeliveryCodeNumber' => [SortAscending::class, SortDescending::class],
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
