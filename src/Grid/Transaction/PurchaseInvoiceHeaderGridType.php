<?php

namespace App\Grid\Transaction;

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

class PurchaseInvoiceHeaderGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['invoiceTaxCodeNumber', 'supplierInvoiceCodeNumber', 'discountValueType', 'discountValue', 'taxMode', 'taxPercentage', 'taxNominal', 'shippingFee', 'subTotal', 'subTotalAfterTaxInclusion', 'grandTotal', 'totalPayment', 'totalReturn', 'remainingPayment', 'isCanceled', 'codeNumberOrdinal', 'codeNumberMonth', 'codeNumberYear', 'createdTransactionDateTime', 'modifiedTransactionDateTime', 'approvedTransactionDateTime', 'transactionDate', 'note'],
                'field_operators_list' => [
                    'invoiceTaxCodeNumber' => [FilterContain::class, FilterNotContain::class],
                    'supplierInvoiceCodeNumber' => [FilterContain::class, FilterNotContain::class],
                    'discountValueType' => [FilterContain::class, FilterNotContain::class],
                    'discountValue' => [FilterEqual::class, FilterNotEqual::class],
                    'taxMode' => [FilterContain::class, FilterNotContain::class],
                    'taxPercentage' => [FilterEqual::class, FilterNotEqual::class],
                    'taxNominal' => [FilterEqual::class, FilterNotEqual::class],
                    'shippingFee' => [FilterEqual::class, FilterNotEqual::class],
                    'subTotal' => [FilterEqual::class, FilterNotEqual::class],
                    'subTotalAfterTaxInclusion' => [FilterEqual::class, FilterNotEqual::class],
                    'grandTotal' => [FilterEqual::class, FilterNotEqual::class],
                    'totalPayment' => [FilterEqual::class, FilterNotEqual::class],
                    'totalReturn' => [FilterEqual::class, FilterNotEqual::class],
                    'remainingPayment' => [FilterEqual::class, FilterNotEqual::class],
                    'isCanceled' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'createdTransactionDateTime' => [FilterEqual::class, FilterNotEqual::class],
                    'modifiedTransactionDateTime' => [FilterEqual::class, FilterNotEqual::class],
                    'approvedTransactionDateTime' => [FilterEqual::class, FilterNotEqual::class],
                    'transactionDate' => [FilterEqual::class, FilterNotEqual::class],
                    'note' => [FilterContain::class, FilterNotContain::class],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['invoiceTaxCodeNumber', 'supplierInvoiceCodeNumber', 'discountValueType', 'discountValue', 'taxMode', 'taxPercentage', 'taxNominal', 'shippingFee', 'subTotal', 'subTotalAfterTaxInclusion', 'grandTotal', 'totalPayment', 'totalReturn', 'remainingPayment', 'isCanceled', 'codeNumberOrdinal', 'codeNumberMonth', 'codeNumberYear', 'createdTransactionDateTime', 'modifiedTransactionDateTime', 'approvedTransactionDateTime', 'transactionDate', 'note'],
                'field_operators_list' => [
                    'invoiceTaxCodeNumber' => [SortAscending::class, SortDescending::class],
                    'supplierInvoiceCodeNumber' => [SortAscending::class, SortDescending::class],
                    'discountValueType' => [SortAscending::class, SortDescending::class],
                    'discountValue' => [SortAscending::class, SortDescending::class],
                    'taxMode' => [SortAscending::class, SortDescending::class],
                    'taxPercentage' => [SortAscending::class, SortDescending::class],
                    'taxNominal' => [SortAscending::class, SortDescending::class],
                    'shippingFee' => [SortAscending::class, SortDescending::class],
                    'subTotal' => [SortAscending::class, SortDescending::class],
                    'subTotalAfterTaxInclusion' => [SortAscending::class, SortDescending::class],
                    'grandTotal' => [SortAscending::class, SortDescending::class],
                    'totalPayment' => [SortAscending::class, SortDescending::class],
                    'totalReturn' => [SortAscending::class, SortDescending::class],
                    'remainingPayment' => [SortAscending::class, SortDescending::class],
                    'isCanceled' => [SortAscending::class, SortDescending::class],
                    'codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'createdTransactionDateTime' => [SortAscending::class, SortDescending::class],
                    'modifiedTransactionDateTime' => [SortAscending::class, SortDescending::class],
                    'approvedTransactionDateTime' => [SortAscending::class, SortDescending::class],
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
