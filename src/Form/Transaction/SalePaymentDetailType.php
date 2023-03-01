<?php

namespace App\Form\Transaction;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedNumberType;
use App\Entity\Transaction\SaleInvoiceHeader;
use App\Entity\Transaction\SalePaymentDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalePaymentDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', FormattedNumberType::class, ['decimals' => 2])
            ->add('memo')
            ->add('isCanceled')
            ->add('account', null, ['choice_label' => 'name'])
            ->add('saleInvoiceHeader', EntityHiddenType::class, ['class' => SaleInvoiceHeader::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SalePaymentDetail::class,
        ]);
    }
}
