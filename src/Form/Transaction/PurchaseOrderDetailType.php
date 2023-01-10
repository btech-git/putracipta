<?php

namespace App\Form\Transaction;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Material;
use App\Entity\Transaction\PurchaseOrderDetail;
use App\Entity\Transaction\PurchaseRequestDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseOrderDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity')
            ->add('unitPrice')
            ->add('material', EntityHiddenType::class, array('class' => Material::class))
            ->add('purchaseRequestDetail', EntityHiddenType::class, array('class' => PurchaseRequestDetail::class))
            ->add('unit', null, ['choice_label' => 'name'])
            ->add('isCanceled')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseOrderDetail::class,
        ]);
    }
}
