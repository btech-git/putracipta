<?php

namespace App\Form\Master;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Paper;
use App\Entity\Master\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code')
            ->add('name')
            ->add('length', null, ['label' => 'Panjang'])
            ->add('width', null, ['label' => 'Lebar'])
            ->add('height', null, ['label' => 'Tinggi'])
            ->add('unit', null, ['choice_label' => 'name', 'label' => 'Satuan'])
            ->add('customer', null, [
                'choice_label' => 'idNameLiteral',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false")
                            ->addOrderBy('e.company', 'ASC');
                },
            ])
            ->add('weight', null, ['label' => 'Berat/piece'])
//            ->add('paper', EntityHiddenType::class, array('class' => Paper::class))
//            ->add('glossiness', ChoiceType::class, ['choices' => [
//                '70' => 70,
//                '80' => 80,
//            ]])
            ->add('note')
            ->add('material')
            ->add('isInactive')
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
                        'maxSizeMessage' => 'Please upload file size smaller than 5MB',
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
