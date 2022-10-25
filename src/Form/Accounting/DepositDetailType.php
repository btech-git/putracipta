<?php

namespace App\Form\Accounting;

use App\Entity\Accounting\DepositDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepositDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description')
            ->add('amount')
            ->add('isCanceled')
            ->add('memo')
            ->add('account')
            ->add('depositHeader')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DepositDetail::class,
        ]);
    }
}
