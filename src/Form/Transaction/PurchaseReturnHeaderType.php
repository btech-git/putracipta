<?php

namespace App\Form\Transaction;

use App\Entity\Transaction\PurchaseReturnHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseReturnHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
            ->add('receiveHeader')
            ->add('createdTransactionUser')
            ->add('modifiedTransactionUser')
            ->add('approvedTransactionUser')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseReturnHeader::class,
        ]);
    }
}
