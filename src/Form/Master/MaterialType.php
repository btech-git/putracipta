<?php

namespace App\Form\Master;

use App\Entity\Master\Material;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaterialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('materialSubCategory', null, ['choice_label' => 'name'])
            ->add('code')
            ->add('name')
            ->add('thickness', null, ['label' => 'Ketebalan'])
            ->add('variant', null, ['label' => 'Varian'])
            ->add('length', null, ['label' => 'Panjang'])
            ->add('width', null, ['label' => 'Lebar'])
            ->add('weight', null, ['label' => 'Berat (GR)'])
            ->add('unit', null, ['choice_label' => 'name', 'label' => 'Satuan'])
            ->add('isInactive')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Material::class,
        ]);
    }
}
