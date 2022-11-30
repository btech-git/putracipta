<?php

namespace App\Form\Transaction;

use App\Entity\Master\Supplier;
use App\Entity\Transaction\PurchaseOrderDetail;
use App\Entity\Transaction\PurchaseOrderHeader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseOrderHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('discountValueType')
            ->add('discountValue')
            ->add('taxMode')
            ->add('shippingFee')
            ->add('purchaseOrderDetails', CollectionType::class, array(
                'entry_type' => PurchaseOrderDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new PurchaseOrderDetail(),
                'label' => false,
            ))
        ;
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            $form = $event->getForm();
            $entity = $event->getData();
            $form->add('supplier', EntityType::class, [
                'class' => Supplier::class,
                'choice_label' => 'name',
                'placeholder' => '',
                'query_builder' => function($repository) use ($entity) {
                    return $repository->createQueryBuilder('e')->where('e = :e')->setParameter('e', $entity->getSupplier());
                },
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseOrderHeader::class,
        ]);
    }
}
