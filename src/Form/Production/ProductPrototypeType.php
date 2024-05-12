<?php

namespace App\Form\Production;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedNumberType;
use App\Entity\Master\Designcode;
use App\Entity\Master\Employee;
use App\Entity\Master\Paper;
use App\Entity\Production\ProductPrototype;
use App\Entity\Production\ProductPrototypeDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductPrototypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('designCode', EntityHiddenType::class, ['class' => Designcode::class])
            ->add('dataSource', ChoiceType::class, ['multiple' => true, 'expanded' => true, 'choices' => [
                'Hard FA' => ProductPrototype::DATA_SOURCE_HARD_FA,
                'Email' => ProductPrototype::DATA_SOURCE_EMAIL,
                'CD' => ProductPrototype::DATA_SOURCE_CD,
                'Sample Cetakan' => ProductPrototype::DATA_SOURCE_PRINT_SAMPLE,
            ]])
            ->add('developmentTypeList', ChoiceType::class, ['multiple' => true, 'expanded' => true, 'choices' => [
                'EP (Engineering Piloting)' => ProductPrototype::DEVELOPMENT_TYPE_EP,
                'FEP (Final Engineering Piloting)' => ProductPrototype::DEVELOPMENT_TYPE_FEP,
                'PP (Production Planning)' => ProductPrototype::DEVELOPMENT_TYPE_PP,
                'PS (Production Schedule)' => ProductPrototype::DEVELOPMENT_TYPE_PS,
            ]])
            ->add('quantityProduction', FormattedNumberType::class, ['decimals' => 0])
            ->add('color')
            ->add('quantityBlade', FormattedNumberType::class, ['decimals' => 0])
            ->add('coatingList', ChoiceType::class, ['multiple' => true, 'expanded' => true, 'choices' => [
                'OPV Matt' => ProductPrototype::COATING_OPV_MATT,
                'OPV Glossy' => ProductPrototype::COATING_OPV_GLOSSY,
                'WB Matt' => ProductPrototype::COATING_WB_MATT,
                'WB Glossy Full' => ProductPrototype::COATING_WB_GLOSSY_FULL,
                'WB Glossy Free' => ProductPrototype::COATING_WB_GLOSSY_FREE,
                'WB Calendering' => ProductPrototype::COATING_WB_CALENDERING,
                'UV Glossy Full' => ProductPrototype::COATING_UV_GLOSSY_FULL,
                'UV Glossy Free' => ProductPrototype::COATING_UV_GLOSSY_FREE,
                'UV Glossy Spot' => ProductPrototype::COATING_UV_GLOSSY_SPOT,
            ]])
            ->add('laminatingList', ChoiceType::class, ['multiple' => true, 'expanded' => true, 'choices' => [
                'Matt' => ProductPrototype::LAMINATING_MATT,
                'Dov' => ProductPrototype::LAMINATING_DOV,
            ]])
            ->add('processList', ChoiceType::class, ['multiple' => true, 'expanded' => true, 'choices' => [
                'Printing' => ProductPrototype::PROCESS_PRINTING,
                'Coating' => ProductPrototype::PROCESS_COATING,
                'Diecut' => ProductPrototype::PROCESS_DIECUT,
                'Emboss' => ProductPrototype::PROCESS_EMBOSS,
                'Hot Stamp' => ProductPrototype::PROCESS_HOTSTAMP,
                'Lem Lock Bottom' => ProductPrototype::PROCESS_LOCK_BOTTOM,
                'Lem Straight Joint' => ProductPrototype::PROCESS_STRAIGHT_JOINT,
                'Jilid Buku' => ProductPrototype::PROCESS_JILID,
            ]])
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('employee', null, [
                'choice_label' => 'name',
//                'query_builder' => function($repository) {
//                    return $repository->createQueryBuilder('e')->andWhere("e.division = '" . Employee::DIVISION_MARKETING . "'");
//                },
            ])
            ->add('customer', null, [
                'choice_label' => 'company',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false")
                            ->addOrderBy('e.company', 'ASC');
                },
            ])
            ->add('paper', EntityHiddenType::class, array('class' => Paper::class))
            ->add('productPrototypeDetails', CollectionType::class, [
                'entry_type' => ProductPrototypeDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new ProductPrototypeDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductPrototype::class,
        ]);
    }
}
