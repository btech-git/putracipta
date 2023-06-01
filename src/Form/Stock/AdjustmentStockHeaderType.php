<?php

namespace App\Form\Stock;

use App\Entity\Stock\AdjustmentStockMaterialDetail;
use App\Entity\Stock\AdjustmentStockPaperDetail;
use App\Entity\Stock\AdjustmentStockProductDetail;
use App\Entity\Stock\AdjustmentStockHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('adjustmentMode', ChoiceType::class, ['multiple' => false, 'expanded' => false, 'choices' => [
                'Material' => AdjustmentStockHeader::ADJUSTMENT_MODE_MATERIAL,
                'Kertas' => AdjustmentStockHeader::ADJUSTMENT_MODE_PAPER,
                'Finished Goods' => AdjustmentStockHeader::ADJUSTMENT_MODE_PRODUCT,
            ]])
            ->add('warehouse', null, ['choice_label' => 'name'])
            ->add('adjustmentStockMaterialDetails', CollectionType::class, [
                'entry_type' => AdjustmentStockMaterialDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new AdjustmentStockMaterialDetail(),
                'label' => false,
            ])
            ->add('adjustmentStockPaperDetails', CollectionType::class, [
                'entry_type' => AdjustmentStockPaperDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new AdjustmentStockPaperDetail(),
                'label' => false,
            ])
            ->add('adjustmentStockProductDetails', CollectionType::class, [
                'entry_type' => AdjustmentStockProductDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new AdjustmentStockProductDetail(),
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
