<?php

namespace App\Form\Accounting;

use App\Entity\Accounting\ExpenseDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpenseDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description')
            ->add('amount')
            ->add('isCanceled')
            ->add('memo')
            ->add('account')
            ->add('expenseHeader')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExpenseDetail::class,
        ]);
    }
}
