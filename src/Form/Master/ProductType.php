<?php

namespace App\Form\Master;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Customer;
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
            ->add('minimumStock')
            ->add('unit', null, ['choice_label' => 'name', 'label' => 'Satuan'])
            ->add('customer', EntityHiddenType::class, ['class' => Customer::class])
//            ->add('customer', null, [
//                'choice_label' => 'name',
//                'query_builder' => function($repository) {
//                    return $repository->createQueryBuilder('e')->addOrderBy('e.company', 'ASC');
//                },
//            ])
            ->add('note')
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
