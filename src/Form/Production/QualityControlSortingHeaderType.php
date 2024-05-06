<?php

namespace App\Form\Production;

use App\Entity\Production\QualityControlSortingHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QualityControlSortingHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('employeeInCharge')
            ->add('isCanceled')
            ->add('codeNumberOrdinal')
            ->add('codeNumberMonth')
            ->add('codeNumberYear')
            ->add('createdTransactionDateTime')
            ->add('modifiedTransactionDateTime')
            ->add('transactionDate')
            ->add('note')
            ->add('codeNumberVersion')
            ->add('masterOrderHeader')
            ->add('customer')
            ->add('createdTransactionUser')
            ->add('modifiedTransactionUser')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QualityControlSortingHeader::class,
        ]);
    }
}
