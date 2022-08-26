<?php

namespace App\Form\Type;

use App\Form\Type\FilterType;
use App\Form\Type\Operator\FilterStringOperators;
use App\Form\Type\PaginationType;
use App\Form\Type\SortType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DataGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, ['field_options' => [
                'name' => FilterStringOperators::class,
                'price' => FilterStringOperators::class,
            ]])
            ->add('sort', SortType::class, ['field_names' => ['name', 'price']])
            ->add('pageSize', ChoiceType::class, ['choices' => [10, 20, 50, 100], 'choice_label' => fn($choice, $key, $value) => $value])
            ->add('pageNumber', IntegerType::class, ['required' => false])
        ;
    }

//    public function configureOptions(OptionsResolver $resolver): void
//    {
//        $resolver->setDefaults([
//            'data_class' => Product::class,
//        ]);
//    }
}
