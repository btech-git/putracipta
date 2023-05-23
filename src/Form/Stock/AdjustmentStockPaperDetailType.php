<?php

namespace App\Form\Stock;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Paper;
use App\Entity\Stock\AdjustmentStockPaperDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdjustmentStockPaperDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantityCurrent')
            ->add('quantityAdjustment')
            ->add('quantityDifference')
            ->add('paper', EntityHiddenType::class, array('class' => Paper::class))
            ->add('isCanceled')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdjustmentStockPaperDetail::class,
        ]);
    }
}
