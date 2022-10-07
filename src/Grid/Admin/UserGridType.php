<?php

namespace App\Grid\Admin;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterEqual;
use App\Common\Data\Operator\FilterNotEqual;
use App\Common\Data\Operator\SortAscending;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\FilterType;
use App\Common\Form\Type\PaginationType;
use App\Common\Form\Type\SortType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['username', 'roles', 'password', 'name', 'email', 'address', 'phone', 'note'],
                'field_operators_list' => [
                    'username' => [FilterEqual::class, FilterNotEqual::class],
                    'roles' => [FilterEqual::class, FilterNotEqual::class],
                    'password' => [FilterEqual::class, FilterNotEqual::class],
                    'name' => [FilterEqual::class, FilterNotEqual::class],
                    'email' => [FilterEqual::class, FilterNotEqual::class],
                    'address' => [FilterEqual::class, FilterNotEqual::class],
                    'phone' => [FilterEqual::class, FilterNotEqual::class],
                    'note' => [FilterEqual::class, FilterNotEqual::class],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['username', 'roles', 'password', 'name', 'email', 'address', 'phone', 'note'],
                'field_operators_list' => [
                    'username' => [SortAscending::class, SortDescending::class],
                    'roles' => [SortAscending::class, SortDescending::class],
                    'password' => [SortAscending::class, SortDescending::class],
                    'name' => [SortAscending::class, SortDescending::class],
                    'email' => [SortAscending::class, SortDescending::class],
                    'address' => [SortAscending::class, SortDescending::class],
                    'phone' => [SortAscending::class, SortDescending::class],
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
