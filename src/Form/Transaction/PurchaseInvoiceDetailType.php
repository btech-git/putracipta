<?php

namespace App\Form\Transaction;

use App\Entity\Transaction\PurchaseInvoiceDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseInvoiceDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity')
            ->add('unitPrice')
            ->add('discount')
            ->add('isCanceled')
            ->add('product')
            ->add('receiveDetail')
            ->add('purchaseInvoiceHeader')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseInvoiceDetail::class,
        ]);
    }
}
