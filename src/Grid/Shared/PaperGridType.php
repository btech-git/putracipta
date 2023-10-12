<?php

namespace App\Grid\Shared;

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
use App\Entity\Master\Paper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaperGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['code', 'name', 'type', 'length', 'width', 'weight', 'unit:name'],
                'field_label_list' => [
                    'name' => 'Code',
                    'weight' => '',
                    'type' => '',
                    'code' => '',
                    'unit:name' => 'Satuan',
                    'length' => 'Panjang',
                    'width' => 'Lebar',
                ],
                'field_operators_list' => [
                    'code' => [FilterContain::class, FilterNotContain::class],
                    'length' => [FilterEqual::class, FilterNotEqual::class],
                    'width' => [FilterEqual::class, FilterNotEqual::class],
                    'weight' => [FilterEqual::class, FilterNotEqual::class],
                    'unit:name' => [FilterContain::class, FilterNotContain::class],
                    'name' => [FilterContain::class, FilterNotContain::class],
                    'type' => [FilterEqual::class, FilterNotEqual::class],
                ],
                'field_value_type_list' => [
                    'type' => ChoiceType::class,
                ],
                'field_value_options_list' => [
                    'type' => ['choices' => ['FSC' => Paper::TYPE_FSC, '000' => Paper::TYPE_NON_FSC]],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['code', 'name', 'length', 'width', 'weight', 'unit:name'],
                'field_label_list' => [
                    'name' => '',
                    'weight' => '',
                    'type' => '',
                    'code' => 'Code',
                    'unit:name' => 'Satuan',
                    'length' => 'Panjang',
                    'width' => 'Lebar',
                ],
                'field_operators_list' => [
                    'code' => [SortAscending::class, SortDescending::class],
                    'length' => [SortAscending::class, SortDescending::class],
                    'width' => [SortAscending::class, SortDescending::class],
                    'type' => [SortAscending::class, SortDescending::class],
                    'weight' => [SortAscending::class, SortDescending::class],
                    'unit:name' => [SortAscending::class, SortDescending::class],
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
