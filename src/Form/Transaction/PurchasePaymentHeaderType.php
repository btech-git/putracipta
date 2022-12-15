<?php

namespace App\Form\Transaction;

use App\Entity\Transaction\PurchasePaymentDetail;
use App\Entity\Transaction\PurchasePaymentHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchasePaymentHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('supplier', null, [
                'choice_label' => 'name',
                'placeholder' => '',
            ])
            ->add('paymentType', null, ['choice_label' => 'name'])
            ->add('purchasePaymentDetails', CollectionType::class, [
                'entry_type' => PurchasePaymentDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new PurchasePaymentDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchasePaymentHeader::class,
        ]);
    }
}
