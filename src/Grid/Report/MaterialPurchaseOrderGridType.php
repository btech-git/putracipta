<?php

namespace App\Grid\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Common\Data\Operator\FilterContain;
use App\Common\Data\Operator\FilterEqual;
use App\Common\Data\Operator\FilterNotBetween;
use App\Common\Data\Operator\FilterNotContain;
use App\Common\Data\Operator\FilterNotEqual;
use App\Common\Data\Operator\SortAscending;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\FilterType;
use App\Common\Form\Type\PaginationType;
use App\Common\Form\Type\SortType;
use App\Entity\Master\Supplier;
use App\Entity\PurchaseHeader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaterialPurchaseOrderGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => [
                    'purchaseOrderHeader:codeNumberOrdinal', 
                    'purchaseOrderHeader:codeNumberMonth', 
                    'purchaseOrderHeader:codeNumberYear', 
                    'purchaseOrderHeader:transactionDate', 
                    'purchaseOrderHeader:supplier', 
                    'name', 
                    'code',
                ],
                'field_label_list' => [
                    'purchaseOrderHeader:codeNumberOrdinal' => 'Code Number',
                    'purchaseOrderHeader:codeNumberMonth' => '',
                    'purchaseOrderHeader:codeNumberYear' => '',
                    'purchaseOrderHeader:transactionDate' => 'Tanggal',
                    'purchaseOrderHeader:supplier' => 'Supplier', 
                    'name' => 'Material',
                ],
                'field_operators_list' => [
                    'purchaseOrderHeader:codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'purchaseOrderHeader:codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'purchaseOrderHeader:codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'purchaseOrderHeader:supplier' => [FilterEqual::class, FilterNotEqual::class],
                    'purchaseOrderHeader:transactionDate' => [FilterBetween::class, FilterNotBetween::class],
                    'name' => [FilterContain::class, FilterNotContain::class],
                    'code' => [FilterContain::class, FilterNotContain::class],
                ],
                'field_value_type_list' => [
                    'purchaseOrderHeader:codeNumberOrdinal' => IntegerType::class,
                    'purchaseOrderHeader:codeNumberMonth' => ChoiceType::class,
                    'purchaseOrderHeader:codeNumberYear' => IntegerType::class,
                    'purchaseOrderHeader:supplier' => EntityType::class,
                ],
                'field_value_options_list' => [
                    'purchaseOrderHeader:codeNumberMonth' => ['choices' => array_flip(PurchaseHeader::MONTH_ROMAN_NUMERALS)],
                    'purchaseOrderHeader:transactionDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                    'purchaseOrderHeader:supplier' => [
                        'class' => Supplier::class, 
                        'choice_label' => 'company',
                        'query_builder' => function($repository) {
                            return $repository->createQueryBuilder('e')
                                    ->andWhere("e.isInactive = false")
                                    ->addOrderBy('e.company', 'ASC');
                        },
                    ],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => [
                    'purchaseOrderHeader:supplier',
                    'purchaseOrderHeader:transactionDate', 
                    'name', 
                    'code',
                    'purchaseOrderHeader:codeNumberYear', 
                    'purchaseOrderHeader:codeNumberMonth', 
                    'purchaseOrderHeader:codeNumberOrdinal'
                ],
                'field_label_list' => [
                    'purchaseOrderHeader:codeNumberOrdinal' => '',
                    'purchaseOrderHeader:codeNumberMonth' => '',
                    'purchaseOrderHeader:codeNumberYear' => 'Code Number',
                    'purchaseOrderHeader:transactionDate' => 'Tanggal',
                    'purchaseOrderHeader:supplier' => 'Supplier',
                    'name' => 'Material',
                ],
                'field_operators_list' => [
                    'purchaseOrderHeader:codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'purchaseOrderHeader:codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'purchaseOrderHeader:codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'purchaseOrderHeader:transactionDate' => [SortAscending::class, SortDescending::class],
                    'purchaseOrderHeader:supplier' => [SortAscending::class, SortDescending::class],
                    'name' => [SortAscending::class, SortDescending::class],
                    'code' => [SortAscending::class, SortDescending::class],
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
