<?php

namespace App\Grid\Transaction;

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
use App\Entity\Master\Warehouse;
use App\Entity\TransactionHeader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeliveryHeaderGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['codeNumberOrdinal', 'codeNumberMonth', 'codeNumberYear', 'transactionDate', 'warehouse', 'customer:company', 'saleOrderReferenceNumbers', 'note'],
                'field_label_list' => [
                    'codeNumberOrdinal' => 'Code Number',
                    'codeNumberMonth' => '',
                    'codeNumberYear' => '',
                    'transactionDate' => 'Tanggal',
                    'customer:company' => 'Customer',
                    'saleOrderReferenceNumbers' => 'PO #',
                    'warehouse' => 'Gudang',
                ],
                'field_operators_list' => [
                    'codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'transactionDate' => [FilterEqual::class, FilterNotEqual::class],
                    'customer:company' => [FilterContain::class, FilterNotContain::class],
                    'warehouse' => [FilterEqual::class, FilterNotEqual::class],
                    'note' => [FilterContain::class, FilterNotContain::class],
                    'saleOrderReferenceNumbers' => [FilterContain::class, FilterNotContain::class],
                ],
                'field_value_type_list' => [
                    'codeNumberOrdinal' => IntegerType::class,
                    'codeNumberMonth' => ChoiceType::class,
                    'codeNumberYear' => IntegerType::class,
                    'warehouse' => EntityType::class,
                ],
                'field_value_options_list' => [
                    'codeNumberMonth' => ['choices' => array_flip(TransactionHeader::MONTH_ROMAN_NUMERALS)],
                    'transactionDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                    'warehouse' => ['class' => Warehouse::class, 'choice_label' => 'name'],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['transactionDate', 'warehouse:name', 'customer:company', 'saleOrderReferenceNumbers', 'note', 'codeNumberYear', 'codeNumberMonth', 'codeNumberOrdinal'],
                'field_label_list' => [
                    'codeNumberOrdinal' => '',
                    'codeNumberMonth' => '',
                    'codeNumberYear' => 'Code Number',
                    'transactionDate' => 'Tanggal',
                    'customer:company' => 'Customer',
                    'warehouse:name' => 'Gudang',
                    'saleOrderReferenceNumbers' => 'PO #',
                ],
                'field_operators_list' => [
                    'codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'transactionDate' => [SortAscending::class, SortDescending::class],
                    'customer:company' => [SortAscending::class, SortDescending::class],
                    'warehouse:name' => [SortAscending::class, SortDescending::class],
                    'note' => [SortAscending::class, SortDescending::class],
                    'saleOrderReferenceNumbers' => [SortAscending::class, SortDescending::class],
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
