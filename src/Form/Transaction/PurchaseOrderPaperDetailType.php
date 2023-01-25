<?php

namespace App\Form\Transaction;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Paper;
use App\Entity\Transaction\PurchaseOrderPaperDetail;
use App\Entity\Transaction\PurchaseRequestPaperDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseOrderPaperDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity')
            ->add('apkiValue')
            ->add('associationPrice')
            ->add('weightPrice')
            ->add('unitPrice')
            ->add('deliveryDate', null, ['widget' => 'single_text'])
            ->add('paper', EntityHiddenType::class, array('class' => Paper::class))
            ->add('purchaseRequestPaperDetail', EntityHiddenType::class, array('class' => PurchaseRequestPaperDetail::class))
            ->add('unit', null, ['choice_label' => 'name'])
            ->add('isCanceled')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseOrderPaperDetail::class,
        ]);
    }
}
