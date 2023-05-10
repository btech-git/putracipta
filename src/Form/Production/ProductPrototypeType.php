<?php

namespace App\Form\Production;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Customer;
use App\Entity\Master\Employee;
use App\Entity\Master\Paper;
use App\Entity\Production\ProductPrototype;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductPrototypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('productCode')
            ->add('productName')
            ->add('measurement')
            ->add('dataSource', ChoiceType::class, ['multiple' => true, 'expanded' => true, 'choices' => [
                'Hard FA' => ProductPrototype::DATA_SOURCE_HARD_FA,
                'Email' => ProductPrototype::DATA_SOURCE_EMAIL,
                'CD' => ProductPrototype::DATA_SOURCE_CD,
                'Sample Cetakan' => ProductPrototype::DATA_SOURCE_PRINT_SAMPLE,
            ]])
            ->add('developmentType', ChoiceType::class, ['multiple' => true, 'expanded' => true, 'choices' => [
                'EP (Engineering Piloting)' => ProductPrototype::DEVELOPMENT_TYPE_EP,
                'FEP (Final Engineering Piloting)' => ProductPrototype::DEVELOPMENT_TYPE_FEP,
                'PP (Production Planning)' => ProductPrototype::DEVELOPMENT_TYPE_PP,
                'PS (Production Schedule)' => ProductPrototype::DEVELOPMENT_TYPE_PS,
            ]])
            ->add('quantityProduction')
            ->add('color')
            ->add('quantityBlade')
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
            ->add('transactionFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5120k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPEG or PNG',
                    ])
                ],
            ])
            ->add('productionDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('employee', null, [
                'choice_label' => 'name',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')->andWhere("e.division = '" . Employee::DIVISION_MARKETING . "'");
                },
            ])
            ->add('customer', EntityHiddenType::class, ['class' => Customer::class])
            ->add('paper', EntityHiddenType::class, array('class' => Paper::class))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductPrototype::class,
        ]);
    }
}
