<?php

namespace App\Form\Transaction;

use App\Entity\Transaction\PurchaseReturnDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseReturnDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity')
            ->add('unitPrice')
            ->add('isCanceled')
            ->add('product')
            ->add('receiveDetail')
            ->add('purchaseReturnHeader')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseReturnDetail::class,
        ]);
    }
}
