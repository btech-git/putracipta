<?php

namespace App\Form\Stock;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Material;
use App\Entity\Stock\AdjustmentStockMaterialDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdjustmentStockMaterialDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantityCurrent')
            ->add('quantityAdjustment')
            ->add('quantityDifference')
            ->add('material', EntityHiddenType::class, array('class' => Material::class))
            ->add('isCanceled')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdjustmentStockMaterialDetail::class,
        ]);
    }
}
