<?php

namespace App\Form\Transaction;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Customer;
use App\Entity\Transaction\SaleInvoiceDetail;
use App\Entity\Transaction\SaleInvoiceHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaleInvoiceHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('invoiceTaxCodeNumber')
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('invoiceTaxDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('discountValueType', ChoiceType::class, ['choices' => [
                'Percentage' => SaleInvoiceHeader::DISCOUNT_VALUE_TYPE_PERCENTAGE,
                'Nominal' => SaleInvoiceHeader::DISCOUNT_VALUE_TYPE_NOMINAL,
            ]])
            ->add('discountValue')
            ->add('taxMode', ChoiceType::class, ['choices' => [
                'Non PPn' => SaleInvoiceHeader::TAX_MODE_NON_TAX,
                'PPn' => SaleInvoiceHeader::TAX_MODE_TAX_EXCLUSION,
            ]])
            ->add('serviceTaxMode', ChoiceType::class, ['choices' => [
                'Non PPh 23' => SaleInvoiceHeader::SERVICE_TAX_MODE_NON_TAX,
                'PPh 23' => SaleInvoiceHeader::SERVICE_TAX_MODE_TAX,
            ]])
            ->add('customer', EntityHiddenType::class, ['class' => Customer::class])
            ->add('saleInvoiceDetails', CollectionType::class, [
                'entry_type' => SaleInvoiceDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new SaleInvoiceDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SaleInvoiceHeader::class,
        ]);
    }
}
