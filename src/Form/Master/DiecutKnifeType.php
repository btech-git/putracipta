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
            ->add('version')
            ->add('upPerSecondKnife')
            ->add('upPerSecondPrint')
            ->add('printingSize')
            ->add('isLocationBobst')
            ->add('isLocationPon')
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
