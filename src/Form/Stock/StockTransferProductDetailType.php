<?php

namespace App\Form\Stock;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedNumberType;
use App\Entity\Master\Product;
use App\Entity\Stock\StockTransferProductDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StockTransferProductDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', FormattedNumberType::class, ['decimals' => 0])
            ->add('memo')
            ->add('isCanceled')
            ->add('product', EntityHiddenType::class, ['class' => Product::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StockTransferProductDetail::class,
        ]);
    }
}
