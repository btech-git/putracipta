<?php

namespace App\Form\Transaction;

use App\Common\Form\Type\EntityTextType;
use App\Entity\Transaction\PurchaseOrderDetail;
use App\Entity\Transaction\PurchaseOrderHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseOrderHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transactionDate')
            ->add('discountType')
            ->add('discountValue')
            ->add('isTaxApplicable')
            ->add('shippingFee')
            ->add('note')
            ->add('supplier', EntityTextType::class, ['class' => PurchaseOrderHeader::class])
            ->add('purchaseOrderDetails', CollectionType::class, array(
                'entry_type' => PurchaseOrderDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new PurchaseOrderDetail(),
                'label' => false,
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseOrderHeader::class,
        ]);
    }
}
