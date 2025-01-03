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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StockSaleOrderDetailGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => [
                    'saleOrderHeader:transactionDate', 
                    'saleOrderHeader:referenceNumber', 
                    'customer:company', 
                    'deliveryDate', 
                    'deliveryDateAlternate', 
                    'product:name'
                ],
                'field_label_list' => [
                    'saleOrderHeader:referenceNumber' => 'PO #',
                    'saleOrderHeader:transactionDate' => 'Tanggal',
                    'customer:company' => 'Customer',
                    'product:name' => 'Product'
                ],
                'field_operators_list' => [
                    'saleOrderHeader:transactionDate' => [FilterEqual::class, FilterNotEqual::class],
                    'deliveryDate' => [FilterEqual::class, FilterNotEqual::class],
                    'customer:company' => [FilterContain::class, FilterNotContain::class],
                    'saleOrderHeader:referenceNumber' => [FilterContain::class, FilterNotContain::class],
                    'product:name' => [FilterContain::class, FilterNotContain::class],
                    'deliveryDateAlternate' => [FilterContain::class, FilterNotContain::class],
                ],
                'field_value_options_list' => [
                    'saleOrderHeader:transactionDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => [
                    'saleOrderHeader:transactionDate', 
                    'customer:company', 
                    'saleOrderHeader:referenceNumber', 
                    'deliveryDate', 
                    'deliveryDateAlternate', 
                    'product:name'
                ],
                'field_label_list' => [
                    'saleOrderHeader:referenceNumber' => 'PO #',
                    'saleOrderHeader:transactionDate' => 'Tanggal',
                    'customer:company' => 'Customer',
                    'product:name' => 'Product'
                ],
                'field_operators_list' => [
                    'saleOrderHeader:transactionDate' => [SortAscending::class, SortDescending::class],
                    'customer:company' => [SortAscending::class, SortDescending::class],
                    'saleOrderHeader:referenceNumber' => [SortAscending::class, SortDescending::class],
                    'product:name' => [SortAscending::class, SortDescending::class],
                    'deliveryDate' => [SortAscending::class, SortDescending::class],
                    'deliveryDateAlternate' => [SortAscending::class, SortDescending::class],
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
