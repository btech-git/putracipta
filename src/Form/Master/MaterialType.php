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
            ->add('thickness')
            ->add('variant')
            ->add('length')
            ->add('width')
            ->add('weight')
            ->add('unit', null, ['choice_label' => 'name'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Material::class,
        ]);
    }
}
