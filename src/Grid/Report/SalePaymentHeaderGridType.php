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
use App\Entity\SaleHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalePaymentHeaderGridType extends AbstractType
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
                    'customer:company', 
                    'saleInvoiceHeader:codeNumberOrdinal', 
                    'saleInvoiceHeader:codeNumberMonth', 
                    'saleInvoiceHeader:codeNumberYear', 
                ],
                'field_label_list' => [
                    'codeNumberOrdinal' => 'Transaction #',
                    'codeNumberMonth' => '',
                    'codeNumberYear' => '',
                    'transactionDate' => 'Tanggal',
                    'customer:company' => 'Customer',
                    'saleInvoiceHeader:codeNumberOrdinal' => 'Invoice #', 
                    'saleInvoiceHeader:codeNumberMonth' => '', 
                    'saleInvoiceHeader:codeNumberYear' => '', 
                ],
                'field_operators_list' => [
                    'codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'transactionDate' => [FilterBetween::class, FilterNotBetween::class],
                    'customer:company' => [FilterContain::class, FilterNotContain::class],
                    'saleInvoiceHeader:codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'saleInvoiceHeader:codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'saleInvoiceHeader:codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                ],
                'field_value_type_list' => [
                    'codeNumberOrdinal' => IntegerType::class,
                    'codeNumberMonth' => ChoiceType::class,
                    'codeNumberYear' => IntegerType::class,
                    'saleInvoiceHeader:codeNumberOrdinal' => IntegerType::class,
                    'saleInvoiceHeader:codeNumberMonth' => ChoiceType::class,
                    'saleInvoiceHeader:codeNumberYear' => IntegerType::class,
                ],
                'field_value_options_list' => [
                    'codeNumberMonth' => ['choices' => array_flip(SaleHeader::MONTH_ROMAN_NUMERALS)],
                    'saleInvoiceHeader:codeNumberMonth' => ['choices' => array_flip(SaleHeader::MONTH_ROMAN_NUMERALS)],
                    'transactionDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => [
                    'codeNumberOrdinal', 
                    'codeNumberMonth', 
                    'codeNumberYear', 
                    'transactionDate', 
                    'customer:company', 
                    'saleInvoiceHeader:codeNumberOrdinal', 
                    'saleInvoiceHeader:codeNumberMonth', 
                    'saleInvoiceHeader:codeNumberYear', 
                ],
                'field_label_list' => [
                    'codeNumberOrdinal' => '',
                    'codeNumberMonth' => '',
                    'codeNumberYear' => 'Code Number',
                    'transactionDate' => 'Tanggal',
                    'customer:company' => 'Customer',
                ],
                'field_operators_list' => [
                    'codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'transactionDate' => [SortAscending::class, SortDescending::class],
                    'customer:company' => [SortAscending::class, SortDescending::class],
                    'saleInvoiceHeader:codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'saleInvoiceHeader:codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'saleInvoiceHeader:codeNumberYear' => [SortAscending::class, SortDescending::class],
                ],
            ])
            ->add('pagination', PaginationType::class, ['size_choices' => [50, 100, 300, 500]])
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
