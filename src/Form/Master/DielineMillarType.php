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
            ->add('customer', null, ['choice_label' => 'company'])
            ->add('name', null, ['label' => 'Kode'])
            ->add('quantity')
            ->add('quantityUpPrinting', null, ['label' => 'Jmlh up'])
            ->add('printingLayout', null, ['label' => 'Kris Layout Cetak'])
            ->add('date', null, ['widget' => 'single_text', 'label' => 'Tanggal'])
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
