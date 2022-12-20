<?php

namespace App\Form\Transaction;

use App\Entity\Transaction\ReceiveDetail;
use App\Entity\Transaction\ReceiveHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReceiveHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('supplierDeliveryCodeNumber')
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('warehouse', null, ['choice_label' => 'name'])
            ->add('note')
            ->add('receiveDetails', CollectionType::class, [
                'entry_type' => ReceiveDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new ReceiveDetail(),
                'label' => false,
            ])
            ->add('purchaseOrderHeader', null, [
                'choice_label' => 'codeNumber',
                'placeholder' => '',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReceiveHeader::class,
        ]);
    }
}
