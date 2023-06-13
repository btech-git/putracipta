<?php

namespace App\Form\Master;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Customer;
use App\Entity\Master\DiecutKnife;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiecutKnifeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customer', EntityHiddenType::class, ['class' => Customer::class])
            ->add('code')
            ->add('name')
            ->add('upPerSecondKnife', null, ['label' => 'Jlh Up/s Pisau'])
            ->add('upPerSecondPrint', null, ['label' => 'Jlh Up/s Cetak'])
            ->add('printingSize', null, ['label' => 'Uk. Kris Cetak (cm)'])
            ->add('isLocationBobst', null, ['label' => 'Lokasi BOBST'])
            ->add('isLocationPon', null, ['label' => 'Lokasi PON'])
            ->add('note', null, ['label' => 'Keterangan'])
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
