<?php

namespace App\Form\Purchase;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedDateType;
use App\Entity\Master\Supplier;
use App\Entity\Purchase\PurchaseInvoiceDetail;
use App\Entity\Purchase\PurchaseInvoiceHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseInvoiceHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//            ->add('invoiceTaxCodeNumber')
            ->add('supplierInvoiceCodeNumber')
            ->add('transactionDate', FormattedDateType::class)
            ->add('invoiceReceivedDate', FormattedDateType::class)
//            ->add('invoiceTaxDate', FormattedDateType::class)
            ->add('discountValueType', ChoiceType::class, ['choices' => [
                'Percentage' => PurchaseInvoiceHeader::DISCOUNT_VALUE_TYPE_PERCENTAGE,
                'Nominal' => PurchaseInvoiceHeader::DISCOUNT_VALUE_TYPE_NOMINAL,
            ]])
            ->add('discountValue')
            ->add('taxMode', ChoiceType::class, ['choices' => [
                'Non PPn' => PurchaseInvoiceHeader::TAX_MODE_NON_TAX,
                'PPn' => PurchaseInvoiceHeader::TAX_MODE_TAX_EXCLUSION,
            ]])
            ->add('note')
            ->add('supplier', EntityHiddenType::class, ['class' => Supplier::class])
            ->add('purchaseInvoiceDetails', CollectionType::class, [
                'entry_type' => PurchaseInvoiceDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new PurchaseInvoiceDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseInvoiceHeader::class,
        ]);
    }
}
