<?php

namespace App\Grid\AccessPoint;

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

class ItemBoxKeyGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['abc', 'def', 'ghi', 'jkl', 'mno', 'pqrs', 'tuv', 'wxyz', 'qwerty', 'dvorak'],
                'field_operators_list' => [
                    'abc' => [FilterEqual::class, FilterNotEqual::class],
                    'def' => [FilterEqual::class, FilterNotEqual::class],
                    'ghi' => [FilterEqual::class, FilterNotEqual::class],
                    'jkl' => [FilterEqual::class, FilterNotEqual::class],
                    'mno' => [FilterEqual::class, FilterNotEqual::class],
                    'pqrs' => [FilterEqual::class, FilterNotEqual::class],
                    'tuv' => [FilterEqual::class, FilterNotEqual::class],
                    'wxyz' => [FilterEqual::class, FilterNotEqual::class],
                    'qwerty' => [FilterEqual::class, FilterNotEqual::class],
                    'dvorak' => [FilterEqual::class, FilterNotEqual::class],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['abc', 'def', 'ghi', 'jkl', 'mno', 'pqrs', 'tuv', 'wxyz', 'qwerty', 'dvorak'],
                'field_operators_list' => [
                    'abc' => [SortAscending::class, SortDescending::class],
                    'def' => [SortAscending::class, SortDescending::class],
                    'ghi' => [SortAscending::class, SortDescending::class],
                    'jkl' => [SortAscending::class, SortDescending::class],
                    'mno' => [SortAscending::class, SortDescending::class],
                    'pqrs' => [SortAscending::class, SortDescending::class],
                    'tuv' => [SortAscending::class, SortDescending::class],
                    'wxyz' => [SortAscending::class, SortDescending::class],
                    'qwerty' => [SortAscending::class, SortDescending::class],
                    'dvorak' => [SortAscending::class, SortDescending::class],
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
