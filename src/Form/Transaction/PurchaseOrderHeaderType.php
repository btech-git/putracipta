<?php

namespace App\Form\Transaction;

use App\Entity\Master\Supplier;
use App\Entity\Transaction\PurchaseOrderDetail;
use App\Entity\Transaction\PurchaseOrderHeader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseOrderHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('discountType')
            ->add('discountValue')
            ->add('isTaxApplicable')
            ->add('shippingFee')
            ->add('note')
            ->add('supplier', EntityType::class, ['class' => Supplier::class, 'choice_label' => 'name', 'required' => false, 'choices' => []])
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
