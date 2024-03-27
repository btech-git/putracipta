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
            ->add('materialSubCategory', null, [
                'choice_label' => 'name',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false")
                            ->andWhere("e.materialCategory <> 1")
                            ->addOrderBy('e.name', 'ASC');
                },
            ])
            ->add('code')
            ->add('name')
            ->add('thickness', null, ['label' => 'Ketebalan'])
            ->add('variant', null, ['label' => 'Varian'])
            ->add('weight', null, ['label' => 'Berat (GSM)'])
            ->add('length', null, ['label' => 'Panjang (cm)'])
            ->add('width', null, ['label' => 'Lebar (cm)'])
            ->add('density')
            ->add('viscosity', null, ['label' => 'Viskositas'])
            ->add('unit', null, ['choice_label' => 'name', 'label' => 'Satuan'])
            ->add('note')
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
