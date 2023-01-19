<?php

namespace App\Form\Transaction;

use App\Entity\Transaction\PurchaseRequestPaperDetail;
use App\Entity\Transaction\PurchaseRequestPaperHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseRequestPaperHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('purchaseRequestPaperDetails', CollectionType::class, [
                'entry_type' => PurchaseRequestPaperDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new PurchaseRequestPaperDetail(),
                'label' => false,
            ])
            ->add('warehouse', null, ['choice_label' => 'name'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseRequestPaperHeader::class,
        ]);
    }
}
