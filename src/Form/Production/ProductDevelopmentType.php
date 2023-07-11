<?php

namespace App\Form\Production;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Production\ProductDevelopment;
use App\Entity\Production\ProductPrototype;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDevelopmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('epArtworkFileDate', null, ['widget' => 'single_text'])
            ->add('epCustomerReviewDate', null, ['widget' => 'single_text'])
            ->add('epSubconDiecutDate', null, ['widget' => 'single_text'])
            ->add('epDielineDevelopmentDate', null, ['widget' => 'single_text'])
            ->add('epImageDeliveryProductionDate', null, ['widget' => 'single_text'])
            ->add('epDiecutDeliveryProductionDate', null, ['widget' => 'single_text'])
            ->add('epDielineDeliveryProductionDate', null, ['widget' => 'single_text'])
            ->add('fepArtworkFileDate', null, ['widget' => 'single_text'])
            ->add('fepCustomerReviewDate', null, ['widget' => 'single_text'])
            ->add('fepSubconDiecutDate', null, ['widget' => 'single_text'])
            ->add('fepDielineDevelopmentDate', null, ['widget' => 'single_text'])
            ->add('fepImageDeliveryProductionDate', null, ['widget' => 'single_text'])
            ->add('fepDiecutDeliveryProductionDate', null, ['widget' => 'single_text'])
            ->add('fepDielineDeliveryProductionDate', null, ['widget' => 'single_text'])
            ->add('psArtworkFileDate', null, ['widget' => 'single_text'])
            ->add('psCustomerReviewDate', null, ['widget' => 'single_text'])
            ->add('psSubconDiecutDate', null, ['widget' => 'single_text'])
            ->add('psDielineDevelopmentDate', null, ['widget' => 'single_text'])
            ->add('psImageDeliveryProductionDate', null, ['widget' => 'single_text'])
            ->add('psDiecutDeliveryProductionDate', null, ['widget' => 'single_text'])
            ->add('psDielineDeliveryProductionDate', null, ['widget' => 'single_text'])
            ->add('productionDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('productPrototype', EntityHiddenType::class, ['class' => ProductPrototype::class])
            ->add('employeeDesigner', null, ['choice_label' => 'name'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductDevelopment::class,
        ]);
    }
}
