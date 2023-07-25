<?php

namespace App\Form\Master;

use App\Entity\Master\DesignCode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DesignCodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customer', null, [
                'choice_label' => 'company',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false")
                            ->addOrderBy('e.company', 'ASC');
                },
            ])
            ->add('code', null, ['label' => 'Kode Produk'])
            ->add('name', null, ['label' => 'Nama'])
            ->add('variant', null, ['label' => 'Varian'])
            ->add('version', null, ['label' => 'Versi'])
            ->add('colorQuantity', null, ['label' => 'Jml Warna'])
            ->add('color', null, ['label' => 'Warna'])
            ->add('pantone')
            ->add('coating', null, ['label' => 'Coating'])
            ->add('quantityPrinting', null, ['label' => 'Jml Up Cetak'])
            ->add('note')
            ->add('isInactive')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DesignCode::class,
        ]);
    }
}
