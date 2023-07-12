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
            ->add('quantity')
            ->add('quantityUpPrinting')
            ->add('printingLayout')
            ->add('note')
            ->add('date', null, ['widget' => 'single_text'])
            ->add('version')
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
