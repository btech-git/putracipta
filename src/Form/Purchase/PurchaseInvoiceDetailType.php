<?php

namespace App\Form\Purchase;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedNumberType;
use App\Entity\Purchase\PurchaseInvoiceDetail;
use App\Entity\Purchase\ReceiveDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseInvoiceDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('unitPrice', FormattedNumberType::class, ['decimals' => 2])
            ->add('receiveDetail', EntityHiddenType::class, ['class' => ReceiveDetail::class])
            ->add('isCanceled')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseInvoiceDetail::class,
        ]);
    }
}
