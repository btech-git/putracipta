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
use App\Entity\Master\Customer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductSaleOrderGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => [
                    'saleOrderHeader:orderReceiveDate', 
                    'saleOrderHeader:referenceNumber', 
                    'note', 
                    'id', 
                    'name',
                    'code',
                    'customer'
                ],
                'field_label_list' => [
                    'saleOrderHeader:orderReceiveDate' => 'Tanggal',
                    'saleOrderHeader:referenceNumber' => 'PO #', 
                    'id' => 'Product',
                ],
                'field_operators_list' => [
                    'saleOrderHeader:orderReceiveDate' => [FilterBetween::class, FilterNotBetween::class],
                    'id' => [FilterEqual::class, FilterNotEqual::class],
                    'name' => [FilterContain::class, FilterNotContain::class],
                    'code' => [FilterContain::class, FilterNotContain::class],
                    'saleOrderHeader:referenceNumber' => [FilterContain::class, FilterNotContain::class],
                    'note' => [FilterContain::class, FilterNotContain::class],
                    'customer' => [FilterEqual::class, FilterNotEqual::class],
                ],
                'field_value_type_list' => [
                    'customer' => EntityType::class,
                ],
                'field_value_options_list' => [
                    'saleOrderHeader:orderReceiveDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                    'customer' => [
                        'class' => Customer::class, 
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
                    'saleOrderHeader:orderReceiveDate', 
                    'saleOrderHeader:referenceNumber', 
                    'note', 
                    'customer', 
                    'id', 
                    'name',
                    'code',
                ],
                'field_label_list' => [
                    'saleOrderHeader:orderReceiveDate' => 'Tanggal',
                    'saleOrderHeader:referenceNumber' => 'PO #', 
                    'id' => 'Product',
                ],
                'field_operators_list' => [
                    'saleOrderHeader:orderReceiveDate' => [SortAscending::class, SortDescending::class],
                    'saleOrderHeader:referenceNumber' => [SortAscending::class, SortDescending::class],
                    'note' => [SortAscending::class, SortDescending::class],
                    'id' => [SortAscending::class, SortDescending::class],
                    'name' => [SortAscending::class, SortDescending::class],
                    'code' => [SortAscending::class, SortDescending::class],
                    'customer' => [SortAscending::class, SortDescending::class],
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
