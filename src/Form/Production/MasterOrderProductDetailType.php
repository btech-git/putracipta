<?php

namespace App\Form\Production;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Production\MasterOrderProductDetail;
use App\Entity\Transaction\SaleOrderDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MasterOrderProductDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isCanceled')
            ->add('quantityStock')
            ->add('quantityUpPrinting1')
            ->add('quantityUpPrinting2')
//            ->add('designCode', null, [
//                'choice_label' => 'name',
//                'choice_attr' => function($choice) {
//                  return ['data-product' => $choice->getProduct()->getId()];
//                },
//                'query_builder' => function($repository) {
//                    return $repository->createQueryBuilder('e')->andWhere("e.product IS NOT NULL");
//                },
//            ])
            ->add('saleOrderDetail', EntityHiddenType::class, ['class' => SaleOrderDetail::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MasterOrderProductDetail::class,
        ]);
    }
}
