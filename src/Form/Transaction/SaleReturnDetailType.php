<?php

namespace App\Form\Transaction;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Product;
use App\Entity\Transaction\SaleReturnDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaleReturnDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity')
            ->add('unitPrice')
            ->add('unit', null, ['choice_label' => 'name'])
            ->add('product', EntityHiddenType::class, ['class' => Product::class])
            ->add('isCanceled')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SaleReturnDetail::class,
        ]);
    }
}
