<?php

namespace App\Form\Transaction;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Customer;
use App\Entity\Transaction\SaleReturnDetail;
use App\Entity\Transaction\SaleReturnHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaleReturnHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('taxMode', ChoiceType::class, ['choices' => [
                'Non Tax' => SaleReturnHeader::TAX_MODE_NON_TAX,
                'Tax Exclusion' => SaleReturnHeader::TAX_MODE_TAX_EXCLUSION,
                'Tax Inclusion' => SaleReturnHeader::TAX_MODE_TAX_INCLUSION,
            ]])
            ->add('saleReturnDetails', CollectionType::class, [
                'entry_type' => SaleReturnDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new SaleReturnDetail(),
                'label' => false,
            ])
            ->add('customer', EntityHiddenType::class, ['class' => Customer::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SaleReturnHeader::class,
        ]);
    }
}
