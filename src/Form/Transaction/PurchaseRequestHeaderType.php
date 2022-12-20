<?php

namespace App\Form\Transaction;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Warehouse;
use App\Entity\Transaction\PurchaseRequestDetail;
use App\Entity\Transaction\PurchaseRequestHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseRequestHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('purchaseRequestDetails', CollectionType::class, [
                'entry_type' => PurchaseRequestDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new PurchaseRequestDetail(),
                'label' => false,
            ])
            ->add('warehouse', EntityHiddenType::class, ['class' => Warehouse::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseRequestHeader::class,
        ]);
    }
}
