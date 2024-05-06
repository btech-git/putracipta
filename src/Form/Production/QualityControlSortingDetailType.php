<?php

namespace App\Form\Production;

use App\Entity\Production\QualityControlSortingDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QualityControlSortingDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantityOrder')
            ->add('totalQuantitySorting')
            ->add('quantityGood')
            ->add('totalQuantityReject')
            ->add('quantityRejectPrinting')
            ->add('quantityRejectCoating')
            ->add('quantityRejectCutting')
            ->add('quantityRejectDiecutting')
            ->add('quantityRejectGlueing')
            ->add('quantityRejectFinishing')
            ->add('quantityRejectOthers')
            ->add('quantityRemaining')
            ->add('memo')
            ->add('isCanceled')
            ->add('product')
            ->add('masterOrderProductDetail')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QualityControlSortingDetail::class,
        ]);
    }
}
