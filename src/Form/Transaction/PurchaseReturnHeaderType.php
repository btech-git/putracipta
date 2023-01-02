<?php

namespace App\Form\Transaction;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Supplier;
use App\Entity\Transaction\PurchaseReturnDetail;
use App\Entity\Transaction\PurchaseReturnHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseReturnHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('taxMode', ChoiceType::class, ['choices' => [
                'Non Tax' => PurchaseReturnHeader::TAX_MODE_NON_TAX,
                'Tax Exclusion' => PurchaseReturnHeader::TAX_MODE_TAX_EXCLUSION,
                'Tax Inclusion' => PurchaseReturnHeader::TAX_MODE_TAX_INCLUSION,
            ]])
            ->add('purchaseReturnDetails', CollectionType::class, [
                'entry_type' => PurchaseReturnDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new PurchaseReturnDetail(),
                'label' => false,
            ])
            ->add('supplier', EntityHiddenType::class, ['class' => Supplier::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseReturnHeader::class,
        ]);
    }
}
