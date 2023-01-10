<?php

namespace App\Form\Transaction;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Supplier;
use App\Entity\Transaction\PurchaseOrderDetail;
use App\Entity\Transaction\PurchaseOrderHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseOrderHeaderPaperType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('deliveryDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('discountValueType', ChoiceType::class, ['choices' => [
                'Percentage' => PurchaseOrderHeader::DISCOUNT_VALUE_TYPE_PERCENTAGE,
                'Nominal' => PurchaseOrderHeader::DISCOUNT_VALUE_TYPE_NOMINAL,
            ]])
            ->add('discountValue')
            ->add('taxMode', ChoiceType::class, ['choices' => [
                'Non Tax' => PurchaseOrderHeader::TAX_MODE_NON_TAX,
                'Tax Exclusion' => PurchaseOrderHeader::TAX_MODE_TAX_EXCLUSION,
                'Tax Inclusion' => PurchaseOrderHeader::TAX_MODE_TAX_INCLUSION,
            ]])
            ->add('purchaseOrderDetails', CollectionType::class, [
                'entry_type' => PurchaseOrderDetailPaperType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new PurchaseOrderDetail(),
                'label' => false,
            ])
            ->add('supplier', EntityHiddenType::class, ['class' => Supplier::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseOrderHeader::class,
        ]);
    }
}
