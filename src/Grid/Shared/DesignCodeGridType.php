<?php

namespace App\Grid\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterContain;
use App\Common\Data\Operator\FilterNotContain;
use App\Common\Data\Operator\SortAscending;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\FilterType;
use App\Common\Form\Type\PaginationType;
use App\Common\Form\Type\SortType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DesignCodeGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['version', 'name', 'note'],
                'field_operators_list' => [
                    'version' => [FilterContain::class, FilterNotContain::class],
                    'name' => [FilterContain::class, FilterNotContain::class],
                    'note' => [FilterContain::class, FilterNotContain::class],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['version', 'name', 'note'],
                'field_operators_list' => [
                    'version' => [SortAscending::class, SortDescending::class],
                    'name' => [SortAscending::class, SortDescending::class],
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
