<?php

namespace App\Form\Master;

use App\Entity\Master\DiecutKnife;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiecutKnifeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customer', null, ['choice_label' => 'name'])
            ->add('name', null, ['label' => 'Kode'])
            ->add('upPerSecondKnife', null, ['label' => 'Up/s Pisau'])
            ->add('upPerSecondPrint', null, ['label' => 'Up/s Cetak'])
            ->add('printingSize', null, ['label' => 'Uk. Kris Cetak'])
            ->add('isLocationBobst', null, ['label' => 'Lokasi BOBST'])
            ->add('isLocationPon', null, ['label' => 'Lokasi PON'])
            ->add('note')
            ->add('isInactive')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DiecutKnife::class,
        ]);
    }
}
