<?php

namespace App\Form\Stock;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Paper;
use App\Entity\Master\Unit;
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
            ->add('paper', EntityHiddenType::class, array('class' => Paper::class))
            ->add('unit', EntityHiddenType::class, array('class' => Unit::class))
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
