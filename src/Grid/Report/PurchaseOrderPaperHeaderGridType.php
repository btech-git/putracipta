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
use App\Entity\Purchase\PurchaseOrderPaperHeader;
use App\Entity\PurchaseHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseOrderPaperHeaderGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => [
                    'codeNumberOrdinal', 
                    'codeNumberMonth', 
                    'codeNumberYear', 
                    'transactionDate', 
                    'supplier:company', 
                    'transactionStatus',
                    'paper:code', 
                    'paper:name',
                    'paper:type', 
                    'paper:weight',
                    'materialSubCategory:code', 
                ],
                'field_label_list' => [
                    'codeNumberOrdinal' => 'Code Number',
                    'codeNumberMonth' => '',
                    'codeNumberYear' => '',
                    'transactionDate' => 'Tanggal',
                    'supplier:company' => 'Supplier',
                    'paper:code' => 'Kode', 
                    'paper:name' => 'Nama',
                    'paper:type' => 'Tipe', 
                    'paper:weight' => 'Berat',
                    'materialSubCategory:code' => 'Category', 
                ],
                'field_operators_list' => [
                    'codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'transactionDate' => [FilterBetween::class, FilterNotBetween::class],
                    'supplier:company' => [FilterContain::class, FilterNotContain::class],
                    'transactionStatus' => [FilterEqual::class, FilterNotEqual::class],
                    'paper:code' => [FilterEqual::class, FilterNotEqual::class],
                    'paper:name' => [FilterContain::class, FilterNotContain::class],
                    'paper:weight' => [FilterEqual::class, FilterNotEqual::class],
                    'paper:type' => [FilterEqual::class, FilterNotEqual::class],
                    'materialSubCategory:code' => [FilterContain::class, FilterNotContain::class],
                ],
                'field_value_type_list' => [
                    'codeNumberOrdinal' => IntegerType::class,
                    'codeNumberMonth' => ChoiceType::class,
                    'codeNumberYear' => IntegerType::class,
                    'paper:type' => ChoiceType::class,
                    'transactionStatus' => ChoiceType::class,
                ],
                'field_value_options_list' => [
                    'codeNumberMonth' => ['choices' => array_flip(PurchaseHeader::MONTH_ROMAN_NUMERALS)],
                    'transactionDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                    'paper:type' => ['choices' => ['000' => 'non', 'FSC' => 'fsc']],
                    'transactionStatus' => ['choices' => [
                        'Draft' => PurchaseOrderPaperHeader::TRANSACTION_STATUS_DRAFT, 
                        'Hold' => PurchaseOrderPaperHeader::TRANSACTION_STATUS_HOLD,
                        'Release' => PurchaseOrderPaperHeader::TRANSACTION_STATUS_RELEASE,
                        'Approved' => PurchaseOrderPaperHeader::TRANSACTION_STATUS_APPROVE,
                        'Reject' => PurchaseOrderPaperHeader::TRANSACTION_STATUS_REJECT,
                        'Partial Receive' => PurchaseOrderPaperHeader::TRANSACTION_STATUS_PARTIAL_RECEIVE,
                        'Full Receive' => PurchaseOrderPaperHeader::TRANSACTION_STATUS_FULL_RECEIVE,
                        'Cancel' => PurchaseOrderPaperHeader::TRANSACTION_STATUS_CANCEL,
                    ]],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => [
                    'transactionDate', 
                    'supplier:company', 
                    'transactionStatus',
                    'paper:code', 
                    'paper:name',
                    'paper:type', 
                    'paper:weight',
                    'materialSubCategory:code', 
                    'codeNumberYear', 
                    'codeNumberMonth', 
                    'codeNumberOrdinal', 
                ],
                'field_label_list' => [
                    'codeNumberOrdinal' => '',
                    'codeNumberMonth' => '',
                    'codeNumberYear' => 'Code Number',
                    'transactionDate' => 'Tanggal',
                    'supplier:company' => 'Supplier',
                    'paper:code' => 'Kode', 
                    'paper:name' => 'Nama',
                    'paper:type' => 'Tipe', 
                    'paper:weight' => 'Berat',
                    'materialSubCategory:code' => 'Category', 
                ],
                'field_operators_list' => [
                    'codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'transactionDate' => [SortAscending::class, SortDescending::class],
                    'supplier:company' => [SortAscending::class, SortDescending::class],
                    'transactionStatus' => [SortAscending::class, SortDescending::class],
                    'paper:code' => [SortAscending::class, SortDescending::class],
                    'paper:name' => [SortAscending::class, SortDescending::class],
                    'materialSubCategory:code' => [SortAscending::class, SortDescending::class],
                    'paper:type' => [SortAscending::class, SortDescending::class],
                    'paper:weight' => [SortAscending::class, SortDescending::class],
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
