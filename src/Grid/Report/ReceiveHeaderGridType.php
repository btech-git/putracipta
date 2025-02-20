<?php

namespace App\Grid\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Common\Data\Operator\FilterEqual;
use App\Common\Data\Operator\FilterNotBetween;
use App\Common\Data\Operator\FilterNotEqual;
use App\Common\Data\Operator\SortAscending;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\FilterType;
use App\Common\Form\Type\PaginationType;
use App\Common\Form\Type\SortType;
use App\Entity\Master\Supplier;
use App\Entity\PurchaseHeader;
use App\Entity\Purchase\ReceiveHeader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReceiveHeaderGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => [
                    'codeNumberOrdinal', 
                    'codeNumberMonth', 
                    'codeNumberYear', 
                    'purchaseOrderHeader:codeNumberOrdinal',
                    'purchaseOrderHeader:codeNumberMonth',
                    'purchaseOrderHeader:codeNumberYear',
                    'purchaseOrderPaperHeader:codeNumberOrdinal',
                    'purchaseOrderPaperHeader:codeNumberMonth',
                    'purchaseOrderPaperHeader:codeNumberYear',
                    'transactionDate', 
                    'supplier', 
                    'transactionType'
                ],
                'field_label_list' => [
                    'codeNumberOrdinal' => 'Receive #',
                    'codeNumberMonth' => '',
                    'codeNumberYear' => '',
                    'purchaseOrderHeader:codeNumberOrdinal' => 'PO Material #',
                    'purchaseOrderHeader:codeNumberMonth' => '',
                    'purchaseOrderHeader:codeNumberYear' => '',
                    'purchaseOrderPaperHeader:codeNumberOrdinal' => 'PO Kertas #',
                    'purchaseOrderPaperHeader:codeNumberMonth' => '',
                    'purchaseOrderPaperHeader:codeNumberYear' => '',
                    'transactionDate' => 'Tanggal',
                    'supplier' => 'Supplier',
                ],
                'field_operators_list' => [
                    'codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'purchaseOrderHeader:codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'purchaseOrderHeader:codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'purchaseOrderHeader:codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'purchaseOrderPaperHeader:codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'purchaseOrderPaperHeader:codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'purchaseOrderPaperHeader:codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'transactionDate' => [FilterBetween::class, FilterNotBetween::class],
                    'supplier' => [FilterEqual::class, FilterNotEqual::class],
                    'transactionType' => [FilterEqual::class, FilterNotEqual::class],
                ],
                'field_value_type_list' => [
                    'codeNumberOrdinal' => IntegerType::class,
                    'codeNumberMonth' => ChoiceType::class,
                    'codeNumberYear' => IntegerType::class,
                    'purchaseOrderHeader:codeNumberOrdinal' => IntegerType::class,
                    'purchaseOrderHeader:codeNumberMonth' => ChoiceType::class,
                    'purchaseOrderHeader:codeNumberYear' => IntegerType::class,
                    'purchaseOrderPaperHeader:codeNumberOrdinal' => IntegerType::class,
                    'purchaseOrderPaperHeader:codeNumberMonth' => ChoiceType::class,
                    'purchaseOrderPaperHeader:codeNumberYear' => IntegerType::class,
                    'transactionType' => ChoiceType::class,
                    'supplier' => EntityType::class,
                ],
                'field_value_options_list' => [
                    'codeNumberMonth' => ['choices' => array_flip(PurchaseHeader::MONTH_ROMAN_NUMERALS)],
                    'purchaseOrderHeader:codeNumberMonth' => ['choices' => array_flip(PurchaseHeader::MONTH_ROMAN_NUMERALS)],
                    'purchaseOrderPaperHeader:codeNumberMonth' => ['choices' => array_flip(PurchaseHeader::MONTH_ROMAN_NUMERALS)],
                    'transactionDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                    'transactionType' => ['choices' => [
                        'Material' => ReceiveHeader::TRANSACTION_TYPE_MATERIAL, 
                        'Paper' => ReceiveHeader::TRANSACTION_TYPE_PAPER,
                    ]],
                    'supplier' => [
                        'class' => Supplier::class, 
                        'choice_label' => 'company',
                        'query_builder' => function($repository) {
                            return $repository->createQueryBuilder('e')
                                    ->andWhere("e.isInactive = false")
                                    ->addOrderBy('e.company', 'ASC');
                        },
                    ],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => [
                    'codeNumberOrdinal', 
                    'codeNumberMonth', 
                    'codeNumberYear', 
                    'purchaseOrderHeader:codeNumberOrdinal',
                    'purchaseOrderHeader:codeNumberMonth',
                    'purchaseOrderHeader:codeNumberYear',
                    'purchaseOrderPaperHeader:codeNumberOrdinal',
                    'purchaseOrderPaperHeader:codeNumberMonth',
                    'purchaseOrderPaperHeader:codeNumberYear',
                    'transactionDate', 
                    'supplier', 
                    'transactionType'
                ],
                'field_label_list' => [
                    'codeNumberOrdinal' => 'Receive #',
                    'codeNumberMonth' => '',
                    'codeNumberYear' => '',
                    'purchaseOrderHeader:codeNumberOrdinal' => 'PO Material #',
                    'purchaseOrderHeader:codeNumberMonth' => '',
                    'purchaseOrderHeader:codeNumberYear' => '',
                    'purchaseOrderPaperHeader:codeNumberOrdinal' => 'PO Kertas #',
                    'purchaseOrderPaperHeader:codeNumberMonth' => '',
                    'purchaseOrderPaperHeader:codeNumberYear' => '',
                    'transactionDate' => 'Tanggal',
                    'supplier' => 'Supplier',
                ],
                'field_operators_list' => [
                    'codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'purchaseOrderHeader:codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'purchaseOrderHeader:codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'purchaseOrderHeader:codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'purchaseOrderPaperHeader:codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'purchaseOrderPaperHeader:codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'purchaseOrderPaperHeader:codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'transactionDate' => [SortAscending::class, SortDescending::class],
                    'supplier' => [SortAscending::class, SortDescending::class],
                    'transactionType' => [SortAscending::class, SortDescending::class],
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
