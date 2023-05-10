<?php

namespace App\Form\Stock;

use App\Entity\Stock\StockTransferDetail;
use App\Entity\Stock\StockTransferHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StockTransferHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('warehouseFrom', null, ['choice_label' => 'name'])
            ->add('warehouseTo', null, ['choice_label' => 'name'])
            ->add('stockTransferDetails', CollectionType::class, [
                'entry_type' => StockTransferDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new StockTransferDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StockTransferHeader::class,
        ]);
    }
}
