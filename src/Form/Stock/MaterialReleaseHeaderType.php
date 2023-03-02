<?php

namespace App\Form\Stock;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Stock\MaterialReleaseDetail;
use App\Entity\Stock\MaterialReleaseHeader;
use App\Entity\Stock\MaterialRequestHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaterialReleaseHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('materialRequestHeader', EntityHiddenType::class, ['class' => MaterialRequestHeader::class])
            ->add('materialReleaseDetails', CollectionType::class, [
                'entry_type' => MaterialReleaseDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new MaterialReleaseDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MaterialReleaseHeader::class,
        ]);
    }
}
