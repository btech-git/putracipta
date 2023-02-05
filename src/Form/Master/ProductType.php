<?php

namespace App\Form\Master;

use App\Entity\Master\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//            ->add('productCategory', null, ['choice_label' => 'name'])
            ->add('code')
            ->add('name')
            ->add('sellingPrice')
            ->add('minimumStock')
            ->add('unit', null, ['choice_label' => 'name', 'label' => 'Satuan'])
            ->add('customer', null, [
                'choice_label' => 'name',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')->orderBy('e.company', 'ASC');
                },
            ])
            ->add('isInactive')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
