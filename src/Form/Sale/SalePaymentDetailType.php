<?php

namespace App\Form\Sale;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedNumberType;
use App\Entity\Sale\SaleInvoiceHeader;
use App\Entity\Sale\SalePaymentDetail;
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
            ->add('account', null, [
                'choice_label' => 'name',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false");
                },
            ])
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
