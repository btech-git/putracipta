<?php

namespace App\Form\Stock;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Material;
use App\Entity\Master\Paper;
use App\Entity\Master\Product;
use App\Entity\Stock\StockTransferDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StockTransferDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity')
            ->add('memo')
            ->add('isCanceled')
            ->add('unit', null, ['choice_label' => 'name'])
            ->add('material', EntityHiddenType::class, ['class' => Material::class])
            ->add('paper', EntityHiddenType::class, ['class' => Paper::class])
            ->add('product', EntityHiddenType::class, ['class' => Product::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StockTransferDetail::class,
        ]);
    }
}
