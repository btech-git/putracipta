<?php

namespace App\Form\Accounting;

use App\Entity\Accounting\JournalVoucherHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JournalVoucherHeaderType extends AbstractType
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
            ->add('createdTransactionUser')
            ->add('modifiedTransactionUser')
            ->add('approvedTransactionUser')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => JournalVoucherHeader::class,
        ]);
    }
}
