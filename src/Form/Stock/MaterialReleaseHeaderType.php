<?php

namespace App\Form\Stock;

use App\Entity\Stock\MaterialReleaseHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaterialReleaseHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('totalQuantity')
            ->add('departmentName')
            ->add('workOrderNumber')
            ->add('partNumber')
            ->add('isCanceled')
            ->add('codeNumberOrdinal')
            ->add('codeNumberMonth')
            ->add('codeNumberYear')
            ->add('createdTransactionDateTime')
            ->add('modifiedTransactionDateTime')
            ->add('transactionDate')
            ->add('note')
            ->add('materialRequestHeader')
            ->add('createdTransactionUser')
            ->add('modifiedTransactionUser')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MaterialReleaseHeader::class,
        ]);
    }
}
