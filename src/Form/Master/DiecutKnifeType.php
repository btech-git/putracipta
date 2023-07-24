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
            ->add('customer', null, ['choice_label' => 'company'])
            ->add('code', null, ['label' => 'Kode'])
            ->add('name', null, ['label' => 'Nama'])
            ->add('upPerSecondKnife', null, ['label' => 'Up/s Pisau'])
            ->add('upPerSecondPrint', null, ['label' => 'Up/s Cetak'])
            ->add('printingSize', null, ['label' => 'Uk. Kris Cetak'])
            ->add('note')
            ->add('isInactive')
            ->add('location', ChoiceType::class, ['label' => 'Location', 'choices' => [
                'BOBST' => DiecutKnife::LOCATION_BOBST,
                'PON' => DiecutKnife::LOCATION_PON,
            ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DiecutKnife::class,
        ]);
    }
}
