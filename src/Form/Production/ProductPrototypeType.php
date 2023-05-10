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
                'EP' => ProductPrototype::DEVELOPMENT_TYPE_EP,
                'FEP' => ProductPrototype::DEVELOPMENT_TYPE_FEP,
                'PS' => ProductPrototype::DEVELOPMENT_TYPE_PS,
            ]])
            ->add('quantityEngineering')
            ->add('quantityProduction')
            ->add('color')
            ->add('quantityBlade')
            ->add('varnishList', ChoiceType::class, ['multiple' => true, 'expanded' => true, 'choices' => [
                'WB' => ProductPrototype::VARNISH_WB,
                'UV/Spot' => ProductPrototype::VARNISH_UV,
                'OPV' => ProductPrototype::VARNISH_OPV,
            ]])
            ->add('laminatingList', ChoiceType::class, ['multiple' => true, 'expanded' => true, 'choices' => [
                'Gloss' => ProductPrototype::LAMINATING_GLOSS,
                'Doff' => ProductPrototype::LAMINATING_DOFF,
            ]])
            ->add('processList', ChoiceType::class, ['multiple' => true, 'expanded' => true, 'choices' => [
                'Diecut' => ProductPrototype::PROCESS_DIECUT,
                'Emboss' => ProductPrototype::PROCESS_EMBOSS,
                'Lock Bottom' => ProductPrototype::PROCESS_LOCK,
                'Lem Joint' => ProductPrototype::PROCESS_JOINT,
                'Hot Stamp' => ProductPrototype::PROCESS_HOTSTAMP,
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
            ->add('paperEp', EntityHiddenType::class, array('class' => Paper::class))
            ->add('paperFep', EntityHiddenType::class, array('class' => Paper::class))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductPrototype::class,
        ]);
    }
}
