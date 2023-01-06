<?php

namespace App\Form\Transaction;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Customer;
use App\Entity\Transaction\SaleOrderDetail;
use App\Entity\Transaction\SaleOrderHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaleOrderHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('deliveryDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('referenceNumber')
            ->add('discountValueType', ChoiceType::class, ['choices' => [
                'Percentage' => SaleOrderHeader::DISCOUNT_VALUE_TYPE_PERCENTAGE,
                'Nominal' => SaleOrderHeader::DISCOUNT_VALUE_TYPE_NOMINAL,
            ]])
            ->add('discountValue')
            ->add('taxMode', ChoiceType::class, ['choices' => [
                'Non Tax' => SaleOrderHeader::TAX_MODE_NON_TAX,
                'Tax Exclusion' => SaleOrderHeader::TAX_MODE_TAX_EXCLUSION,
                'Tax Inclusion' => SaleOrderHeader::TAX_MODE_TAX_INCLUSION,
            ]])
            ->add('saleOrderDetails', CollectionType::class, [
                'entry_type' => SaleOrderDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new SaleOrderDetail(),
                'label' => false,
            ])
            ->add('customer', EntityHiddenType::class, ['class' => Customer::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SaleOrderHeader::class,
        ]);
    }
}
