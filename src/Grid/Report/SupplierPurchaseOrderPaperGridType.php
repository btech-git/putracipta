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
use App\Entity\Master\Supplier;
use App\Entity\PurchaseHeader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SupplierPurchaseOrderPaperGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => [
                    'purchaseOrderPaperHeader:codeNumberOrdinal', 
                    'purchaseOrderPaperHeader:codeNumberMonth', 
                    'purchaseOrderPaperHeader:codeNumberYear', 
                    'purchaseOrderPaperHeader:transactionDate', 
                    'id',
                ],
                'field_label_list' => [
                    'purchaseOrderPaperHeader:codeNumberOrdinal' => 'Code Number',
                    'purchaseOrderPaperHeader:codeNumberMonth' => '',
                    'purchaseOrderPaperHeader:codeNumberYear' => '',
                    'purchaseOrderPaperHeader:transactionDate' => 'Tanggal',
                    'id' => 'Supplier',
                ],
                'field_operators_list' => [
                    'purchaseOrderPaperHeader:codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'purchaseOrderPaperHeader:codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'purchaseOrderPaperHeader:codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'purchaseOrderPaperHeader:transactionDate' => [FilterBetween::class, FilterNotBetween::class],
                    'id' => [FilterEqual::class, FilterNotEqual::class],
                ],
                'field_value_type_list' => [
                    'purchaseOrderPaperHeader:codeNumberOrdinal' => IntegerType::class,
                    'purchaseOrderPaperHeader:codeNumberMonth' => ChoiceType::class,
                    'purchaseOrderPaperHeader:codeNumberYear' => IntegerType::class,
                    'id' => EntityType::class,
                ],
                'field_value_options_list' => [
                    'purchaseOrderPaperHeader:codeNumberMonth' => ['choices' => array_flip(PurchaseHeader::MONTH_ROMAN_NUMERALS)],
                    'purchaseOrderPaperHeader:transactionDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                    'id' => [
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
                    'purchaseOrderPaperHeader:transactionDate', 
                    'company', 
                    'purchaseOrderPaperHeader:codeNumberYear', 
                    'purchaseOrderPaperHeader:codeNumberMonth', 
                    'purchaseOrderPaperHeader:codeNumberOrdinal'
                ],
                'field_label_list' => [
                    'purchaseOrderPaperHeader:codeNumberOrdinal' => '',
                    'purchaseOrderPaperHeader:codeNumberMonth' => '',
                    'purchaseOrderPaperHeader:codeNumberYear' => 'Code Number',
                    'purchaseOrderPaperHeader:transactionDate' => 'Tanggal',
                    'company' => 'Supplier',
                ],
                'field_operators_list' => [
                    'purchaseOrderPaperHeader:codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'purchaseOrderPaperHeader:codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'purchaseOrderPaperHeader:codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'purchaseOrderPaperHeader:transactionDate' => [SortAscending::class, SortDescending::class],
                    'company' => [SortAscending::class, SortDescending::class],
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
