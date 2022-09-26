<?php

namespace App\Form\Transaction;

use App\Entity\Transaction\PurchaseInvoiceHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseInvoiceHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codeNumberOrdinal')
            ->add('codeNumberMonth')
            ->add('codeNumberYear')
            ->add('transactionDate')
            ->add('invoiceTaxCodeNumber')
            ->add('supplierInvoiceCodeNumber')
            ->add('discountType')
            ->add('discountNominal')
            ->add('taxPercentage')
            ->add('taxNominal')
            ->add('shippingFee')
            ->add('subTotal')
            ->add('grandTotal')
            ->add('totalPayment')
            ->add('totalReturn')
            ->add('remainingPayment')
            ->add('note')
            ->add('createdTransactionDateTime')
            ->add('modifiedTransactionDateTime')
            ->add('approvedTransactionDateTime')
            ->add('supplier')
            ->add('receiveHeader')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseInvoiceHeader::class,
        ]);
    }
}
