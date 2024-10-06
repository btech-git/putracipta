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
use App\Entity\Master\Employee;
use App\Repository\Master\DivisionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaleOrderDetailGridType extends AbstractType
{
    private DivisionRepository $divisionRepository;

    public function __construct(DivisionRepository $divisionRepository)
    {
        $this->divisionRepository = $divisionRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => [
                    'saleOrderHeader:orderReceiveDate', 
                    'saleOrderHeader:referenceNumber', 
                    'saleOrderHeader:employee', 
                    'saleOrderHeader:customer', 
                    'product:code', 
                    'product:name'
                ],
                'field_label_list' => [
                    'saleOrderHeader:orderReceiveDate' => 'Tanggal',
                    'saleOrderHeader:customer' => 'Customer',
                    'saleOrderHeader:employee' => 'Marketing',
                    'saleOrderHeader:referenceNumber' => 'PO #', 
                    'product:code' => 'Kode Produk', 
                    'product:name' => 'Nama Produk'
                ],
                'field_operators_list' => [
                    'saleOrderHeader:orderReceiveDate' => [FilterBetween::class, FilterNotBetween::class],
                    'saleOrderHeader:customer' => [FilterEqual::class, FilterNotEqual::class],
                    'saleOrderHeader:employee' => [FilterEqual::class, FilterNotEqual::class],
                    'saleOrderHeader:referenceNumber' => [FilterContain::class, FilterNotContain::class],
                    'product:code' => [FilterContain::class, FilterNotContain::class],
                    'product:name' => [FilterContain::class, FilterNotContain::class],
                ],
                'field_value_type_list' => [
                    'saleOrderHeader:customer' => EntityType::class,
                    'saleOrderHeader:employee' => EntityType::class,
                ],
                'field_value_options_list' => [
                    'saleOrderHeader:orderReceiveDate' => ['attr' => ['data-controller' => 'flatpickr-element']],
                    'saleOrderHeader:customer' => [
                        'class' => Customer::class, 
                        'choice_label' => 'company',
                        'query_builder' => function($repository) {
                            return $repository->createQueryBuilder('c')
                                    ->andWhere("c.isInactive = false")
                                    ->addOrderBy('c.company', 'ASC');
                        },
                    ],
                    'saleOrderHeader:employee' => [
                        'class' => Employee::class, 
                        'choice_label' => 'name',
                        'query_builder' => function($repository) {
                            return $repository->createQueryBuilder('m')
                                ->andWhere("m.division = :division")->setParameter('division', $this->divisionRepository->findMarketingRecord())
                                ->andWhere("m.isInactive = false");
                        },
                    ],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => [
                    'saleOrderHeader:orderReceiveDate', 
                    'saleOrderHeader:referenceNumber', 
                    'saleOrderHeader:employee', 
                    'saleOrderHeader:customer', 
                    'product:code', 
                    'product:name'
                ],
                'field_label_list' => [
                    'saleOrderHeader:orderReceiveDate' => 'Tanggal',
                    'saleOrderHeader:customer' => 'Customer',
                    'saleOrderHeader:employee' => 'Marketing',
                    'saleOrderHeader:referenceNumber' => 'PO #', 
                    'product:code' => 'Kode Produk', 
                    'product:name' => 'Nama Produk'
                ],
                'field_operators_list' => [
                    'saleOrderHeader:orderReceiveDate' => [SortAscending::class, SortDescending::class],
                    'saleOrderHeader:customer' => [SortAscending::class, SortDescending::class],
                    'saleOrderHeader:employee' => [SortAscending::class, SortDescending::class],
                    'saleOrderHeader:referenceNumber' => [SortAscending::class, SortDescending::class],
                    'product:code' => [SortAscending::class, SortDescending::class],
                    'product:name' => [SortAscending::class, SortDescending::class],
                ],
            ])
            ->add('pagination', PaginationType::class, ['size_choices' => [50, 100, 300, 500]])
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
