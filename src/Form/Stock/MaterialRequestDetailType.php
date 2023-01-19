<?php

namespace App\Form\Stock;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Material;
use App\Entity\Stock\MaterialRequestDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaterialRequestDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity')
            ->add('memo')
            ->add('isCanceled')
            ->add('unit', null, ['choice_label' => 'name'])
            ->add('material', EntityHiddenType::class, ['class' => Material::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MaterialRequestDetail::class,
        ]);
    }
}
