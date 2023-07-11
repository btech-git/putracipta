<?php

namespace App\Grid\Production;

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

class ProductDevelopmentGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['developmentType', 'epArtworkFileDate', 'epArtWorkFileTime', 'epCustomerReviewDate', 'epCustomerReviewTime', 'epSubconDiecutDate', 'epSubConDiecutTime', 'epDielineDevelopmentDate', 'epDielineDevelopmentTime', 'epImageDeliveryProductionDate', 'epImageDeliveryProductionTime', 'epDiecutDeliveryProductionDate', 'epDiecutDeliveryProductionTime', 'epDielineDeliveryProductionDate', 'epDielineDeliveryProductionTime', 'fepArtworkFileDate', 'fepArtworkFileTime', 'fepCustomerReviewDate', 'fepCustomerReviewTime', 'fepSubconDiecutDate', 'fepSubconDiecutTime', 'fepDielineDevelopmentDate', 'fepDielienDevelopmentTime', 'fepImageDeliveryProductionDate', 'fepImageDeliveryProductionTime', 'fepDiecutDeliveryProductionDate', 'fepDiecutDeliveryProductionTime', 'fepDielineDeliveryProductionDate', 'fepDielineDeliveryProductionTime', 'psArtworkFileDate', 'psArtworkFileTime', 'psCustomerReviewDate', 'psCustomerReviewTime', 'psSubconDiecutDate', 'psSubconDiecutTime', 'psDielineDevelopmentDate', 'psDielieneDevelopmentTime', 'psDielineDevelopmentTime', 'psImageDeliveryProductionDate', 'psImageDeliveryProductionTime', 'psDiecutDeliveryProductionDate', 'psDiecutDeliveryProductionTime', 'psDielineDeliveryProductionDate', 'psDielineDeliveryProductionTime', 'isCanceled', 'isRead', 'codeNumberOrdinal', 'codeNumberMonth', 'codeNumberYear', 'createdProductionDateTime', 'modifiedProductionDateTime', 'productionDate', 'note'],
                'field_operators_list' => [
                    'developmentType' => [FilterEqual::class, FilterNotEqual::class],
                    'epArtworkFileDate' => [FilterEqual::class, FilterNotEqual::class],
                    'epArtWorkFileTime' => [FilterEqual::class, FilterNotEqual::class],
                    'epCustomerReviewDate' => [FilterEqual::class, FilterNotEqual::class],
                    'epCustomerReviewTime' => [FilterEqual::class, FilterNotEqual::class],
                    'epSubconDiecutDate' => [FilterEqual::class, FilterNotEqual::class],
                    'epSubConDiecutTime' => [FilterEqual::class, FilterNotEqual::class],
                    'epDielineDevelopmentDate' => [FilterEqual::class, FilterNotEqual::class],
                    'epDielineDevelopmentTime' => [FilterEqual::class, FilterNotEqual::class],
                    'epImageDeliveryProductionDate' => [FilterEqual::class, FilterNotEqual::class],
                    'epImageDeliveryProductionTime' => [FilterEqual::class, FilterNotEqual::class],
                    'epDiecutDeliveryProductionDate' => [FilterEqual::class, FilterNotEqual::class],
                    'epDiecutDeliveryProductionTime' => [FilterEqual::class, FilterNotEqual::class],
                    'epDielineDeliveryProductionDate' => [FilterEqual::class, FilterNotEqual::class],
                    'epDielineDeliveryProductionTime' => [FilterEqual::class, FilterNotEqual::class],
                    'fepArtworkFileDate' => [FilterEqual::class, FilterNotEqual::class],
                    'fepArtworkFileTime' => [FilterEqual::class, FilterNotEqual::class],
                    'fepCustomerReviewDate' => [FilterEqual::class, FilterNotEqual::class],
                    'fepCustomerReviewTime' => [FilterEqual::class, FilterNotEqual::class],
                    'fepSubconDiecutDate' => [FilterEqual::class, FilterNotEqual::class],
                    'fepSubconDiecutTime' => [FilterEqual::class, FilterNotEqual::class],
                    'fepDielineDevelopmentDate' => [FilterEqual::class, FilterNotEqual::class],
                    'fepDielienDevelopmentTime' => [FilterEqual::class, FilterNotEqual::class],
                    'fepImageDeliveryProductionDate' => [FilterEqual::class, FilterNotEqual::class],
                    'fepImageDeliveryProductionTime' => [FilterEqual::class, FilterNotEqual::class],
                    'fepDiecutDeliveryProductionDate' => [FilterEqual::class, FilterNotEqual::class],
                    'fepDiecutDeliveryProductionTime' => [FilterEqual::class, FilterNotEqual::class],
                    'fepDielineDeliveryProductionDate' => [FilterEqual::class, FilterNotEqual::class],
                    'fepDielineDeliveryProductionTime' => [FilterEqual::class, FilterNotEqual::class],
                    'psArtworkFileDate' => [FilterEqual::class, FilterNotEqual::class],
                    'psArtworkFileTime' => [FilterEqual::class, FilterNotEqual::class],
                    'psCustomerReviewDate' => [FilterEqual::class, FilterNotEqual::class],
                    'psCustomerReviewTime' => [FilterEqual::class, FilterNotEqual::class],
                    'psSubconDiecutDate' => [FilterEqual::class, FilterNotEqual::class],
                    'psSubconDiecutTime' => [FilterEqual::class, FilterNotEqual::class],
                    'psDielineDevelopmentDate' => [FilterEqual::class, FilterNotEqual::class],
                    'psDielieneDevelopmentTime' => [FilterEqual::class, FilterNotEqual::class],
                    'psDielineDevelopmentTime' => [FilterEqual::class, FilterNotEqual::class],
                    'psImageDeliveryProductionDate' => [FilterEqual::class, FilterNotEqual::class],
                    'psImageDeliveryProductionTime' => [FilterEqual::class, FilterNotEqual::class],
                    'psDiecutDeliveryProductionDate' => [FilterEqual::class, FilterNotEqual::class],
                    'psDiecutDeliveryProductionTime' => [FilterEqual::class, FilterNotEqual::class],
                    'psDielineDeliveryProductionDate' => [FilterEqual::class, FilterNotEqual::class],
                    'psDielineDeliveryProductionTime' => [FilterEqual::class, FilterNotEqual::class],
                    'isCanceled' => [FilterEqual::class, FilterNotEqual::class],
                    'isRead' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberOrdinal' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberMonth' => [FilterEqual::class, FilterNotEqual::class],
                    'codeNumberYear' => [FilterEqual::class, FilterNotEqual::class],
                    'createdProductionDateTime' => [FilterEqual::class, FilterNotEqual::class],
                    'modifiedProductionDateTime' => [FilterEqual::class, FilterNotEqual::class],
                    'productionDate' => [FilterEqual::class, FilterNotEqual::class],
                    'note' => [FilterContain::class, FilterNotContain::class],
                ],
            ])
            ->add('sort', SortType::class, [
                'field_names' => ['developmentType', 'epArtworkFileDate', 'epArtWorkFileTime', 'epCustomerReviewDate', 'epCustomerReviewTime', 'epSubconDiecutDate', 'epSubConDiecutTime', 'epDielineDevelopmentDate', 'epDielineDevelopmentTime', 'epImageDeliveryProductionDate', 'epImageDeliveryProductionTime', 'epDiecutDeliveryProductionDate', 'epDiecutDeliveryProductionTime', 'epDielineDeliveryProductionDate', 'epDielineDeliveryProductionTime', 'fepArtworkFileDate', 'fepArtworkFileTime', 'fepCustomerReviewDate', 'fepCustomerReviewTime', 'fepSubconDiecutDate', 'fepSubconDiecutTime', 'fepDielineDevelopmentDate', 'fepDielienDevelopmentTime', 'fepImageDeliveryProductionDate', 'fepImageDeliveryProductionTime', 'fepDiecutDeliveryProductionDate', 'fepDiecutDeliveryProductionTime', 'fepDielineDeliveryProductionDate', 'fepDielineDeliveryProductionTime', 'psArtworkFileDate', 'psArtworkFileTime', 'psCustomerReviewDate', 'psCustomerReviewTime', 'psSubconDiecutDate', 'psSubconDiecutTime', 'psDielineDevelopmentDate', 'psDielieneDevelopmentTime', 'psDielineDevelopmentTime', 'psImageDeliveryProductionDate', 'psImageDeliveryProductionTime', 'psDiecutDeliveryProductionDate', 'psDiecutDeliveryProductionTime', 'psDielineDeliveryProductionDate', 'psDielineDeliveryProductionTime', 'isCanceled', 'isRead', 'codeNumberOrdinal', 'codeNumberMonth', 'codeNumberYear', 'createdProductionDateTime', 'modifiedProductionDateTime', 'productionDate', 'note'],
                'field_operators_list' => [
                    'developmentType' => [SortAscending::class, SortDescending::class],
                    'epArtworkFileDate' => [SortAscending::class, SortDescending::class],
                    'epArtWorkFileTime' => [SortAscending::class, SortDescending::class],
                    'epCustomerReviewDate' => [SortAscending::class, SortDescending::class],
                    'epCustomerReviewTime' => [SortAscending::class, SortDescending::class],
                    'epSubconDiecutDate' => [SortAscending::class, SortDescending::class],
                    'epSubConDiecutTime' => [SortAscending::class, SortDescending::class],
                    'epDielineDevelopmentDate' => [SortAscending::class, SortDescending::class],
                    'epDielineDevelopmentTime' => [SortAscending::class, SortDescending::class],
                    'epImageDeliveryProductionDate' => [SortAscending::class, SortDescending::class],
                    'epImageDeliveryProductionTime' => [SortAscending::class, SortDescending::class],
                    'epDiecutDeliveryProductionDate' => [SortAscending::class, SortDescending::class],
                    'epDiecutDeliveryProductionTime' => [SortAscending::class, SortDescending::class],
                    'epDielineDeliveryProductionDate' => [SortAscending::class, SortDescending::class],
                    'epDielineDeliveryProductionTime' => [SortAscending::class, SortDescending::class],
                    'fepArtworkFileDate' => [SortAscending::class, SortDescending::class],
                    'fepArtworkFileTime' => [SortAscending::class, SortDescending::class],
                    'fepCustomerReviewDate' => [SortAscending::class, SortDescending::class],
                    'fepCustomerReviewTime' => [SortAscending::class, SortDescending::class],
                    'fepSubconDiecutDate' => [SortAscending::class, SortDescending::class],
                    'fepSubconDiecutTime' => [SortAscending::class, SortDescending::class],
                    'fepDielineDevelopmentDate' => [SortAscending::class, SortDescending::class],
                    'fepDielienDevelopmentTime' => [SortAscending::class, SortDescending::class],
                    'fepImageDeliveryProductionDate' => [SortAscending::class, SortDescending::class],
                    'fepImageDeliveryProductionTime' => [SortAscending::class, SortDescending::class],
                    'fepDiecutDeliveryProductionDate' => [SortAscending::class, SortDescending::class],
                    'fepDiecutDeliveryProductionTime' => [SortAscending::class, SortDescending::class],
                    'fepDielineDeliveryProductionDate' => [SortAscending::class, SortDescending::class],
                    'fepDielineDeliveryProductionTime' => [SortAscending::class, SortDescending::class],
                    'psArtworkFileDate' => [SortAscending::class, SortDescending::class],
                    'psArtworkFileTime' => [SortAscending::class, SortDescending::class],
                    'psCustomerReviewDate' => [SortAscending::class, SortDescending::class],
                    'psCustomerReviewTime' => [SortAscending::class, SortDescending::class],
                    'psSubconDiecutDate' => [SortAscending::class, SortDescending::class],
                    'psSubconDiecutTime' => [SortAscending::class, SortDescending::class],
                    'psDielineDevelopmentDate' => [SortAscending::class, SortDescending::class],
                    'psDielieneDevelopmentTime' => [SortAscending::class, SortDescending::class],
                    'psDielineDevelopmentTime' => [SortAscending::class, SortDescending::class],
                    'psImageDeliveryProductionDate' => [SortAscending::class, SortDescending::class],
                    'psImageDeliveryProductionTime' => [SortAscending::class, SortDescending::class],
                    'psDiecutDeliveryProductionDate' => [SortAscending::class, SortDescending::class],
                    'psDiecutDeliveryProductionTime' => [SortAscending::class, SortDescending::class],
                    'psDielineDeliveryProductionDate' => [SortAscending::class, SortDescending::class],
                    'psDielineDeliveryProductionTime' => [SortAscending::class, SortDescending::class],
                    'isCanceled' => [SortAscending::class, SortDescending::class],
                    'isRead' => [SortAscending::class, SortDescending::class],
                    'codeNumberOrdinal' => [SortAscending::class, SortDescending::class],
                    'codeNumberMonth' => [SortAscending::class, SortDescending::class],
                    'codeNumberYear' => [SortAscending::class, SortDescending::class],
                    'createdProductionDateTime' => [SortAscending::class, SortDescending::class],
                    'modifiedProductionDateTime' => [SortAscending::class, SortDescending::class],
                    'productionDate' => [SortAscending::class, SortDescending::class],
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
