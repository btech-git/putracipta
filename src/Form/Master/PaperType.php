<?php

namespace App\Form\Master;

use App\Entity\Master\Paper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaperType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code')
            ->add('name')
            ->add('length', null, ['label' => 'Panjang'])
            ->add('width', null, ['label' => 'Lebar'])
            ->add('weight', null, ['label' => 'Berat (GSM)'])
            ->add('pricingMode', null, ['label' => 'Pricing Mode'])
            ->add('unit', null, ['choice_label' => 'name', 'label' => 'Satuan'])
            ->add('isInactive')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Paper::class,
        ]);
    }
}
