<?php

namespace App\Form\Transaction;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Transaction\PurchaseOrderDetail;
use App\Entity\Transaction\ReceiveDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReceiveDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('receivedQuantity')
            ->add('isCanceled')
            ->add('usageDate', null, ['widget' => 'single_text'])
            ->add('memo')
            ->add('purchaseOrderDetail', EntityHiddenType::class, ['class' => PurchaseOrderDetail::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReceiveDetail::class,
        ]);
    }
}
