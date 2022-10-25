<?php

namespace App\Form\Accounting;

use App\Entity\Accounting\ExpenseHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpenseHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isCanceled')
            ->add('codeNumberOrdinal')
            ->add('codeNumberMonth')
            ->add('codeNumberYear')
            ->add('createdTransactionDateTime')
            ->add('modifiedTransactionDateTime')
            ->add('approvedTransactionDateTime')
            ->add('transactionDate')
            ->add('note')
            ->add('account')
            ->add('createdTransactionUser')
            ->add('modifiedTransactionUser')
            ->add('approvedTransactionUser')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExpenseHeader::class,
        ]);
    }
}
