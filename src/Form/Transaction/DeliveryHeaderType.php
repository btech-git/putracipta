<?php

namespace App\Form\Transaction;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Transaction\SaleOrderHeader;
use App\Entity\Transaction\DeliveryDetail;
use App\Entity\Transaction\DeliveryHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeliveryHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('warehouse', null, ['choice_label' => 'name'])
            ->add('note')
            ->add('driverName')
            ->add('vehicleType')
            ->add('vehicleNumber')
            ->add('deliveryDetails', CollectionType::class, [
                'entry_type' => DeliveryDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new DeliveryDetail(),
                'label' => false,
            ])
            ->add('saleOrderHeader', EntityHiddenType::class, ['class' => SaleOrderHeader::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DeliveryHeader::class,
        ]);
    }
}
