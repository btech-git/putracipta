<?php

namespace App\Form\Master;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\DielineMillar;
use App\Entity\Master\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DielineMillarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customer', null, [
                'choice_label' => 'idNameLiteral',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false")
                            ->addOrderBy('e.company', 'ASC');
                },
            ])
//            ->add('name', null, ['label' => 'Nama Produk'])
            ->add('code', null, ['label' => 'Kode'])
            ->add('version', null, ['label' => 'Revisi'])
            ->add('product', EntityHiddenType::class, ['class' => Product::class])
            ->add('quantityUpPrinting', null, ['label' => 'Jmlh Up Cetak'])
            ->add('printingLayout', null, ['label' => 'Kris Layout Cetak'])
            ->add('date', null, ['widget' => 'single_text', 'label' => 'Tanggal Pembuatan'])
            ->add('note')
            ->add('isInactive')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DielineMillar::class,
        ]);
    }
}
