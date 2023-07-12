<?php

namespace App\Form\Master;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Customer;
use App\Entity\Master\DesignCode;
use App\Entity\Master\DiecutKnife;
use App\Entity\Master\DielineMillar;
use App\Entity\Master\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//            ->add('productCategory', null, ['choice_label' => 'name'])
            ->add('code')
            ->add('name')
            ->add('length', null, ['label' => 'Panjang'])
            ->add('width', null, ['label' => 'Lebar'])
            ->add('height', null, ['label' => 'Tebal'])
            ->add('unit', null, ['choice_label' => 'name', 'label' => 'Satuan'])
            ->add('customer', EntityHiddenType::class, ['class' => Customer::class])
            ->add('weight', null, ['label' => 'Berat'])
            ->add('glossiness')
            ->add('note')
            ->add('isInactive')
            ->add('designCodes', CollectionType::class, [
                'entry_type' => DesignCodeType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new DesignCode(),
                'label' => false,
            ])
            ->add('diecutKnives', CollectionType::class, [
                'entry_type' => DiecutKnifeType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new DiecutKnife(),
                'label' => false,
            ])
            ->add('dielineMillars', CollectionType::class, [
                'entry_type' => DielineMillarType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new DielineMillar(),
                'label' => false,
            ])
            ->add('transactionFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5120k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'application/pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPEG or PNG or PDF',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
