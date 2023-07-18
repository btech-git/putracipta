<?php

namespace App\Grid\Master;

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

class DiecutKnifeGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['upPerSecondKnife', 'upPerSecondPrint', 'printingSize', 'isLocationBobst', 'isLocationPon', 'note', 'version', 'isInactive', 'name'],
                'field_operators_list' => [
                    'upPerSecondKnife' => [FilterEqual::class, FilterNotEqual::class],
                    'upPerSecondPrint' => [FilterEqual::class, FilterNotEqual::class],
                    'printingSize' => [FilterContain::class, FilterNotContain::class],
                    'isLocationBobst' => [FilterEqual::class, FilterNotEqual::class],
                    'isLocationPon' => [FilterEqual::class, FilterNotEqual::class],
                    'note' => [FilterContain::class, FilterNotContain::class],
                    'version' => [FilterContain::class, FilterNotContain::class],
                    'isInactive' => [FilterEqual::class, FilterNotEqual::class],
                    'name' => [FilterContain::class, FilterNotContain::class],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['upPerSecondKnife', 'upPerSecondPrint', 'printingSize', 'isLocationBobst', 'isLocationPon', 'note', 'version', 'isInactive', 'name'],
                'field_operators_list' => [
                    'upPerSecondKnife' => [SortAscending::class, SortDescending::class],
                    'upPerSecondPrint' => [SortAscending::class, SortDescending::class],
                    'printingSize' => [SortAscending::class, SortDescending::class],
                    'isLocationBobst' => [SortAscending::class, SortDescending::class],
                    'isLocationPon' => [SortAscending::class, SortDescending::class],
                    'note' => [SortAscending::class, SortDescending::class],
                    'version' => [SortAscending::class, SortDescending::class],
                    'isInactive' => [SortAscending::class, SortDescending::class],
                    'name' => [SortAscending::class, SortDescending::class],
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
