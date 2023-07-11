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
            ->add('version')
            ->add('variant')
            ->add('color')
            ->add('pantone')
            ->add('quantityPrinting1')
            ->add('quantityPrinting2')
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
