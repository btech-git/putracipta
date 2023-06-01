<?php

namespace App\Form\Stock;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Stock\InventoryReleasePaperDetail;
use App\Entity\Stock\InventoryRequestPaperDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InventoryReleasePaperDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity')
            ->add('memo')
            ->add('isCanceled')
            ->add('inventoryRequestPaperDetail', EntityHiddenType::class, ['class' => InventoryRequestPaperDetail::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InventoryReleasePaperDetail::class,
        ]);
    }
}
