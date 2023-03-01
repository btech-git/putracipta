<?php

namespace App\Form\Stock;

use App\Entity\Stock\AdjustmentStockDetail;
use App\Entity\Stock\AdjustmentStockHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdjustmentStockHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('warehouse', null, ['choice_label' => 'name'])
            ->add('adjustmentStockDetails', CollectionType::class, [
                'entry_type' => AdjustmentStockDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new AdjustmentStockDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdjustmentStockHeader::class,
        ]);
    }
}
