<?php

namespace App\Form\Accounting;

use App\Entity\Accounting\JournalVoucherDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JournalVoucherDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('debitAmount')
            ->add('creditAmount')
            ->add('isCanceled')
            ->add('memo')
            ->add('account')
            ->add('journalVoucherHeader')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => JournalVoucherDetail::class,
        ]);
    }
}
