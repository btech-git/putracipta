<?php

namespace App\Form\Transaction;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Customer;
use App\Entity\Transaction\SalePaymentDetail;
use App\Entity\Transaction\SalePaymentHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalePaymentHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('referenceNumber')
            ->add('administrationFee')
//            ->add('returnAmount')
            ->add('referenceDate', null, ['widget' => 'single_text'])
            ->add('customer', EntityHiddenType::class, ['class' => Customer::class])
            ->add('paymentType', null, ['choice_label' => 'name'])
            ->add('salePaymentDetails', CollectionType::class, [
                'entry_type' => SalePaymentDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new SalePaymentDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SalePaymentHeader::class,
        ]);
    }
}
