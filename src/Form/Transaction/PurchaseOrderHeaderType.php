<?php

namespace App\Form\Transaction;

use App\Entity\Transaction\PurchaseOrderHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseOrderHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('discountValueType')
            ->add('discountValue')
            ->add('taxMode')
            ->add('taxPercentage')
            ->add('taxNominal')
            ->add('shippingFee')
            ->add('subTotal')
            ->add('subTotalAfterTaxInclusion')
            ->add('grandTotal')
            ->add('isCanceled')
            ->add('codeNumberOrdinal')
            ->add('codeNumberMonth')
            ->add('codeNumberYear')
            ->add('createdTransactionDateTime')
            ->add('modifiedTransactionDateTime')
            ->add('approvedTransactionDateTime')
            ->add('transactionDate')
            ->add('note')
            ->add('supplier')
            ->add('createdTransactionUser')
            ->add('modifiedTransactionUser')
            ->add('approvedTransactionUser')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseOrderHeader::class,
        ]);
    }
}
