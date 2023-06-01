<?php

namespace App\Form\Stock;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Material;
use App\Entity\Stock\InventoryReleaseMaterialDetail;
use App\Entity\Stock\InventoryRequestMaterialDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InventoryReleaseMaterialDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity')
            ->add('memo')
            ->add('isCanceled')
            ->add('material', EntityHiddenType::class, array('class' => Material::class))
            ->add('inventoryRequestMaterialDetail', EntityHiddenType::class, ['class' => InventoryRequestMaterialDetail::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InventoryReleaseMaterialDetail::class,
        ]);
    }
}
