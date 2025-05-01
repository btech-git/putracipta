<?php

namespace App\Grid\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Common\Data\Operator\FilterEqual;
use App\Common\Data\Operator\FilterNotBetween;
use App\Common\Data\Operator\FilterNotEqual;
use App\Common\Data\Operator\SortAscending;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\FilterType;
use App\Common\Form\Type\PaginationType;
use App\Common\Form\Type\SortType;
use App\Entity\Master\MaterialSubCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaperTransactionFlowGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => [
                    'transactionDate',
                    '_paper:id',
                    '_materialSubCategory:id',
                ],
                'field_label_list' => [
                    'transactionDate' => 'Tanggal',
                    '_paper:id' => 'Paper',
                    '_materialSubCategory:id' => 'Category',
                ],
                'field_operators_list' => [
                    'transactionDate' => [FilterBetween::class, FilterNotBetween::class],
                    '_paper:id' => [FilterEqual::class, FilterNotEqual::class],
                    '_materialSubCategory:id' => [FilterEqual::class, FilterNotEqual::class],
                ],
                'field_value_type_list' => [
                    '_materialSubCategory:id' => EntityType::class,
                ],
                'field_value_options_list' => [
                    'transactionDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                    '_materialSubCategory:id' => [
                        'class' => MaterialSubCategory::class, 
                        'choice_label' => 'name',
                        'query_builder' => function($repository) {
                            return $repository->createQueryBuilder('e')
                                    ->andWhere("e.isInactive = false")
                                    ->andWhere("IDENTITY(e.materialCategory) = 7")
                                    ->addOrderBy('e.name', 'ASC');
                        },
                    ],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => [
                    'transactionDate',
                    '_paper:id',
                ],
                'field_label_list' => [
                    'transactionDate' => 'Tanggal',
                    '_paper:id' => 'Paper',
                ],
                'field_operators_list' => [
                    'transactionDate' => [SortAscending::class, SortDescending::class],
                    '_paper:id' => [SortAscending::class, SortDescending::class],
                ],
            ])
            ->add('pagination', PaginationType::class, ['size_choices' => [10, 20, 50, 100]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DataCriteria::class,
            'csrf_protection' => false,
        ]);
    }
}
