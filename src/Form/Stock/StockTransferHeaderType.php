<?php

namespace App\Form\Stock;

use App\Entity\Stock\StockTransferMaterialDetail;
use App\Entity\Stock\StockTransferPaperDetail;
use App\Entity\Stock\StockTransferProductDetail;
use App\Entity\Stock\StockTransferHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('transferMode', ChoiceType::class, ['multiple' => false, 'expanded' => false, 'choices' => [
                'Material' => StockTransferHeader::MODE_MATERIAL,
                'Kertas' => StockTransferHeader::MODE_PAPER,
                'Finished Goods' => StockTransferHeader::MODE_PRODUCT,
            ]])
            ->add('warehouseFrom', null, ['choice_label' => 'name'])
            ->add('warehouseTo', null, ['choice_label' => 'name'])
            ->add('stockTransferMaterialDetails', CollectionType::class, [
                'entry_type' => StockTransferMaterialDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new StockTransferMaterialDetail(),
                'label' => false,
            ])
            ->add('stockTransferPaperDetails', CollectionType::class, [
                'entry_type' => StockTransferPaperDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new StockTransferPaperDetail(),
                'label' => false,
            ])
            ->add('stockTransferProductDetails', CollectionType::class, [
                'entry_type' => StockTransferProductDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new StockTransferProductDetail(),
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
