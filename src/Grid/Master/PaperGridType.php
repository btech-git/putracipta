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

class PaperGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['code', 'name', 'length', 'width', 'weight', 'isInactive'],
                'field_label_list' => [
                    'name' => 'Nama',
                    'code' => 'Code',
                    'length' => 'Panjang',
                    'width' => 'Lebar',
                    'weight' => 'GSM',
                ],
                'field_operators_list' => [
                    'code' => [FilterContain::class, FilterNotContain::class],
                    'length' => [FilterEqual::class, FilterNotEqual::class],
                    'width' => [FilterEqual::class, FilterNotEqual::class],
                    'weight' => [FilterEqual::class, FilterNotEqual::class],
                    'name' => [FilterContain::class, FilterNotContain::class],
                    'isInactive' => [FilterEqual::class, FilterNotEqual::class],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['code', 'name', 'length', 'width', 'weight', 'isInactive'],
                'field_label_list' => [
                    'name' => 'Nama',
                    'code' => 'Code',
                    'length' => 'Panjang',
                    'width' => 'Lebar',
                    'weight' => 'GSM',
                ],
                'field_operators_list' => [
                    'code' => [SortAscending::class, SortDescending::class],
                    'length' => [SortAscending::class, SortDescending::class],
                    'width' => [SortAscending::class, SortDescending::class],
                    'weight' => [SortAscending::class, SortDescending::class],
                    'name' => [SortAscending::class, SortDescending::class],
                    'isInactive' => [SortAscending::class, SortDescending::class],
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
