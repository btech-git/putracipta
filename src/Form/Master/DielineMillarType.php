<?php

namespace App\Form\Master;

use App\Entity\Master\DielineMillar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DielineMillarType extends AbstractType
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
            ->add('code', null, ['label' => 'Kode'])
            ->add('name', null, ['label' => 'Nama'])
//            ->add('quantity')
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
