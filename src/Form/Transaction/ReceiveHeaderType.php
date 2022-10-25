<?php

namespace App\Form\Transaction;

use App\Entity\Transaction\ReceiveHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReceiveHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('totalQuantity')
            ->add('supplierDeliveryCodeNumber')
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
            ->add('purchaseOrderHeader')
            ->add('createdTransactionUser')
            ->add('modifiedTransactionUser')
            ->add('approvedTransactionUser')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReceiveHeader::class,
        ]);
    }
}
