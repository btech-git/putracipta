<?php

namespace App\Form\Transaction;

use App\Entity\Transaction\PurchaseInvoiceDetail;
use App\Entity\Transaction\PurchaseInvoiceHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseInvoiceHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('invoiceTaxCodeNumber')
            ->add('supplierInvoiceCodeNumber')
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('receiveHeader', null, [
                'choice_label' => 'codeNumber',
                'placeholder' => '',
            ])
            ->add('purchaseInvoiceDetails', CollectionType::class, [
                'entry_type' => PurchaseInvoiceDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new PurchaseInvoiceDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseInvoiceHeader::class,
        ]);
    }
}
