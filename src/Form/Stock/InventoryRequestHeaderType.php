<?php

namespace App\Form\Stock;

use App\Entity\Stock\InventoryRequestMaterialDetail;
use App\Entity\Stock\InventoryRequestPaperDetail;
use App\Entity\Stock\InventoryRequestHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InventoryRequestHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('departmentName')
            ->add('workOrderNumber')
            ->add('partNumber')
            ->add('warehouse', null, ['choice_label' => 'name'])
            ->add('pickupDate', null, ['widget' => 'single_text'])
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('requestMode', ChoiceType::class, ['multiple' => false, 'expanded' => false, 'choices' => [
                'Material' => InventoryRequestHeader::MODE_MATERIAL,
                'Kertas' => InventoryRequestHeader::MODE_PAPER,
            ]])
            ->add('inventoryRequestMaterialDetails', CollectionType::class, [
                'entry_type' => InventoryRequestMaterialDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new InventoryRequestMaterialDetail(),
                'label' => false,
            ])
            ->add('inventoryRequestPaperDetails', CollectionType::class, [
                'entry_type' => InventoryRequestPaperDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new InventoryRequestPaperDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InventoryRequestHeader::class,
        ]);
    }
}
