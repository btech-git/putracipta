<?php

namespace App\Form\Accounting;

use App\Entity\Accounting\DepositDetail;
use App\Entity\Accounting\DepositHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepositHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('account', null, ['choice_label' => 'name'])
            ->add('depositDetails', CollectionType::class, [
                'entry_type' => DepositDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new DepositDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DepositHeader::class,
        ]);
    }
}
