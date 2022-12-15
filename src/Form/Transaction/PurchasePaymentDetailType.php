<?php

namespace App\Form\Transaction;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Transaction\PurchaseInvoiceHeader;
use App\Entity\Transaction\PurchasePaymentDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchasePaymentDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount')
            ->add('memo')
            ->add('isCanceled')
            ->add('account')
            ->add('purchaseInvoiceHeader', EntityHiddenType::class, array('class' => PurchaseInvoiceHeader::class))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchasePaymentDetail::class,
        ]);
    }
}
