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
            ->add('customer', null, ['choice_label' => 'name'])
            ->add('name', null, ['label' => 'Kode'])
            ->add('variant', null, ['label' => 'Varian'])
            ->add('version', null, ['label' => 'Versi'])
            ->add('color', null, ['label' => 'Warna'])
            ->add('pantone')
            ->add('quantityPrinting1', null, ['label' => 'Jml Up Cetak 1'])
            ->add('quantityPrinting2', null, ['label' => 'Jml Up Cetak 2'])
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
