<?php

namespace App\Form\Purchase;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedNumberType;
use App\Entity\Master\Material;
use App\Entity\Purchase\PurchaseOrderDetail;
use App\Entity\Purchase\PurchaseRequestDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseOrderDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', FormattedNumberType::class, ['decimals' => 0])
            ->add('unitPrice', FormattedNumberType::class, ['decimals' => 2])
            ->add('deliveryDate', null, ['widget' => 'single_text'])
            ->add('material', EntityHiddenType::class, array('class' => Material::class))
            ->add('purchaseRequestDetail', EntityHiddenType::class, array('class' => PurchaseRequestDetail::class))
            ->add('unit', null, [
                'choice_label' => 'name', 
                'label' => 'Satuan',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false");
                },
            ])
            ->add('isTransactionClosed')
            ->add('isCanceled')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseOrderDetail::class,
        ]);
    }
}
