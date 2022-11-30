<?php

namespace App\Form\Transaction;

use App\Entity\Transaction\PurchaseOrderDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseOrderDetailType_2 extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity')
            ->add('unitPrice')
            ->add('discount')
            ->add('isCanceled')
            ->add('product')
            ->add('purchaseOrderHeader')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseOrderDetail::class,
        ]);
    }
}
