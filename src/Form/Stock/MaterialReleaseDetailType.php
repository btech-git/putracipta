<?php

namespace App\Form\Stock;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Stock\MaterialRequestDetail;
use App\Entity\Stock\MaterialReleaseDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaterialReleaseDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity')
            ->add('memo')
            ->add('unit', null, ['choice_label' => 'name'])
            ->add('materialRequestDetail', EntityHiddenType::class, ['class' => MaterialRequestDetail::class])
            ->add('isCanceled')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MaterialReleaseDetail::class,
        ]);
    }
}
