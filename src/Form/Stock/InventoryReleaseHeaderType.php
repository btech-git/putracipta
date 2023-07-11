<?php

namespace App\Form\Stock;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Stock\InventoryReleaseHeader;
use App\Entity\Stock\InventoryReleaseMaterialDetail;
use App\Entity\Stock\InventoryReleasePaperDetail;
use App\Entity\Stock\InventoryRequestHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InventoryReleaseHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('warehouse', null, ['choice_label' => 'name'])
            ->add('inventoryRequestHeader', EntityHiddenType::class, ['class' => InventoryRequestHeader::class])
            ->add('inventoryReleaseMaterialDetails', CollectionType::class, [
                'entry_type' => InventoryReleaseMaterialDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new InventoryReleaseMaterialDetail(),
                'label' => false,
            ])
            ->add('inventoryReleasePaperDetails', CollectionType::class, [
                'entry_type' => InventoryReleasePaperDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new InventoryReleasePaperDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InventoryReleaseHeader::class,
        ]);
    }
}
