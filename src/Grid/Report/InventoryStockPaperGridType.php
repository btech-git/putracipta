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
use App\Entity\Master\Warehouse;
use App\Entity\StockHeader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InventoryStockPaperGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => [
                    'code', 
                    'name', 
                    'type', 
                    'weight', 
                    'materialSubCategory:name', 
                    'inventory:codeNumberOrdinal', 
                    'inventory:codeNumberMonth', 
                    'inventory:codeNumberYear', 
                    'inventory:transactionDate', 
                    'inventory:warehouse'
                ],
                'field_label_list' => [
                    'inventory:codeNumberOrdinal' => 'Code Number',
                    'inventory:codeNumberMonth' => '',
                    'inventory:codeNumberYear' => '',
                    'inventory:transactionDate' => 'Tanggal',
                    'inventory:warehouse' => 'Gudang',
                    'materialSubCategory:name' => 'Category', 
                ],
                'field_operators_list' => [
                    'inventory:codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'inventory:codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'inventory:codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'inventory:transactionDate' => [FilterBetween::class, FilterNotBetween::class],
                    'inventory:warehouse' => [FilterEqual::class, FilterNotEqual::class],
                    'code' => [FilterContain::class, FilterNotContain::class],
                    'name' => [FilterContain::class, FilterNotContain::class],
                    'weight' => [FilterEqual::class, FilterNotEqual::class],
                    'type' => [FilterEqual::class, FilterNotEqual::class],
                    'materialSubCategory:name' => [FilterContain::class, FilterNotContain::class],
                ],
                'field_value_type_list' => [
                    'inventory:codeNumberOrdinal' => IntegerType::class,
                    'inventory:codeNumberMonth' => ChoiceType::class,
                    'inventory:codeNumberYear' => IntegerType::class,
                    'inventory:warehouse' => EntityType::class,
                    'type' => ChoiceType::class,
                ],
                'field_value_options_list' => [
                    'inventory:codeNumberMonth' => ['choices' => array_flip(StockHeader::MONTH_ROMAN_NUMERALS)],
                    'inventory:transactionDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                    'inventory:warehouse' => ['class' => Warehouse::class, 'choice_label' => 'name'],
                    'type' => ['choices' => ['000' => 'non', 'FSC' => 'fsc']],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => [
                    'code', 
                    'name', 
                    'type', 
                    'weight', 
                    'materialSubCategory:name', 
                    'inventory:transactionDate', 
                    'inventory:warehouse', 
                    'inventory:codeNumberYear', 
                    'inventory:codeNumberMonth', 
                    'inventory:codeNumberOrdinal'
                ],
                'field_label_list' => [
                    'inventory:codeNumberOrdinal' => '',
                    'inventory:codeNumberMonth' => '',
                    'inventory:codeNumberYear' => 'Code Number',
                    'inventory:transactionDate' => 'Tanggal',
                    'inventory:warehouse' => 'Gudang',
                    'materialSubCategory:name' => 'Category',
                ],
                'field_operators_list' => [
                    'code' => [SortAscending::class, SortDescending::class],
                    'name' => [SortAscending::class, SortDescending::class],
                    'weight' => [SortAscending::class, SortDescending::class],
                    'type' => [SortAscending::class, SortDescending::class],
                    'materialSubCategory:name' => [SortAscending::class, SortDescending::class],
                    'inventory:codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'inventory:codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'inventory:codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'inventory:transactionDate' => [SortAscending::class, SortDescending::class],
                    'inventory:warehouse' => [SortAscending::class, SortDescending::class],
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
