<?php

namespace App\Form\Transaction;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Supplier;
use App\Entity\Transaction\PurchaseOrderPaperDetail;
use App\Entity\Transaction\PurchaseOrderPaperHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseOrderPaperHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('discountValueType', ChoiceType::class, ['choices' => [
                'Percentage' => PurchaseOrderPaperHeader::DISCOUNT_VALUE_TYPE_PERCENTAGE,
                'Nominal' => PurchaseOrderPaperHeader::DISCOUNT_VALUE_TYPE_NOMINAL,
            ]])
            ->add('discountValue')
            ->add('taxMode', ChoiceType::class, ['choices' => [
                'Non PPn' => PurchaseOrderPaperHeader::TAX_MODE_NON_TAX,
                'Exclude PPn' => PurchaseOrderPaperHeader::TAX_MODE_TAX_EXCLUSION,
                'Include PPn' => PurchaseOrderPaperHeader::TAX_MODE_TAX_INCLUSION,
            ]])
            ->add('purchaseOrderPaperDetails', CollectionType::class, [
                'entry_type' => PurchaseOrderPaperDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new PurchaseOrderPaperDetail(),
                'label' => false,
            ])
            ->add('supplier', EntityHiddenType::class, ['class' => Supplier::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseOrderPaperHeader::class,
        ]);
    }
}
