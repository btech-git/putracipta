<?php

namespace App\Form\Transaction;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedNumberType;
use App\Entity\Master\Product;
use App\Entity\Transaction\SaleOrderDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaleOrderDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity')
            ->add('unitPrice', FormattedNumberType::class, ['decimals' => 2])
            ->add('deliveryDate', null, ['widget' => 'single_text'])
            ->add('product', EntityHiddenType::class, array('class' => Product::class))
            ->add('unit', null, ['choice_label' => 'name'])
            ->add('isCanceled')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SaleOrderDetail::class,
        ]);
    }
}
