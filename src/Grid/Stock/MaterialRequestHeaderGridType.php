<?php

namespace App\Grid\Stock;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterContain;
use App\Common\Data\Operator\FilterEqual;
use App\Common\Data\Operator\FilterNotContain;
use App\Common\Data\Operator\FilterNotEqual;
use App\Common\Data\Operator\SortAscending;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\FilterType;
use App\Common\Form\Type\PaginationType;
use App\Common\Form\Type\SortType;
use App\Entity\StockHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaterialRequestHeaderGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['codeNumberOrdinal', 'codeNumberMonth', 'codeNumberYear', 'transactionDate', 'departmentName', 'workOrderNumber', 'partNumber', 'note'],
                'field_label_list' => [
                    'codeNumberOrdinal' => 'Code Number',
                    'codeNumberMonth' => '',
                    'codeNumberYear' => '',
                    'transactionDate' => 'Tanggal',
                    'departmentName' => 'Departemen',
                    'workOrderNumber' => 'WO #',
                    'partNumber' => 'Part #',
                ],
                'field_operators_list' => [
                    'codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'transactionDate' => [FilterEqual::class, FilterNotEqual::class],
                    'departmentName' => [FilterContain::class, FilterNotContain::class],
                    'workOrderNumber' => [FilterContain::class, FilterNotContain::class],
                    'partNumber' => [FilterContain::class, FilterNotContain::class],
                    'note' => [FilterContain::class, FilterNotContain::class],
                ],
                'field_value_type_list' => [
                    'codeNumberOrdinal' => IntegerType::class,
                    'codeNumberMonth' => ChoiceType::class,
                    'codeNumberYear' => IntegerType::class,
                ],
                'field_value_options_list' => [
                    'codeNumberMonth' => ['choices' => array_flip(StockHeader::MONTH_ROMAN_NUMERALS)],
                    'transactionDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['transactionDate', 'departmentName', 'workOrderNumber', 'partNumber', 'note', 'id'],
                'field_label_list' => [
                    'id' => 'Code Number',
                    'transactionDate' => 'Tanggal',
                    'departmentName' => 'Departemen',
                    'workOrderNumber' => 'WO #',
                    'partNumber' => 'Part #',
                ],
                'field_operators_list' => [
                    'id' => [SortAscending::class, SortDescending::class],
                    'departmentName' => [SortAscending::class, SortDescending::class],
                    'workOrderNumber' => [SortAscending::class, SortDescending::class],
                    'partNumber' => [SortAscending::class, SortDescending::class],
                    'transactionDate' => [SortAscending::class, SortDescending::class],
                    'note' => [SortAscending::class, SortDescending::class],
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
