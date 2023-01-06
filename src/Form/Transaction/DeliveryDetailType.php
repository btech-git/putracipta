<?php

namespace App\Form\Transaction;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Transaction\SaleOrderDetail;
use App\Entity\Transaction\DeliveryDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeliveryDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('deliveredQuantity')
            ->add('isCanceled')
            ->add('lotNumber')
            ->add('packaging')
            ->add('fscCode')
            ->add('saleOrderDetail', EntityHiddenType::class, ['class' => SaleOrderDetail::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DeliveryDetail::class,
        ]);
    }
}
