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

class MasterOrderGridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', FilterType::class, [
                'field_names' => ['printingMachine', 'designCode', 'productCode', 'quantityOrder', 'quantityStock', 'measurement', 'color', 'finishing', 'quantityPrinting', 'mountageSize', 'isUsingHotStamping', 'deliveryDate', 'printingStatus', 'dieCutBlade', 'dieCutBladeNumber', 'dieLineFilmNumber', 'layoutModelFileExtension', 'insitPercentage', 'quantityPaper', 'paperMountage', 'paperPlanoLength', 'paperPlanoWidth', 'cuttingLengthSize1', 'cuttingWidthSize1', 'cuttingLengthSize2', 'cuttingWidthSize2', 'paperRequirement', 'printingInsit', 'paperTotal', 'inkCyanPercentage', 'inkCyanWeight', 'inkMagentaPercentage', 'inkMagentaWeight', 'inkYellowPercentage', 'inkYellowWeight', 'inkBlackPercentage', 'inkBlackWeight', 'inkK1Percentage', 'inkK1Weight', 'inkK2Percentage', 'inkK2Weight', 'inkK3Percentage', 'inkK3Weight', 'inkK4Percentage', 'inkK4Weight', 'inkOpvPercentage', 'inkOpvWeight', 'inkLaminatingSize', 'inkLaminatingQuantity', 'packagingGlueQuantity', 'packagingGlueWeight', 'packagingRubberQuantity', 'packagingRubberWeight', 'packagingPaperQuantity', 'packagingPaperWeight', 'packagingBoxQuantity', 'packagingBoxWeight', 'packagingTapeLargeQuantity', 'packagingTapeLargeSize', 'packagingTapeSmallQuantity', 'packagingTapeSmallSize', 'packagingPlasticQuantity', 'packagingPlasticSize', 'isCanceled', 'isRead', 'codeNumberOrdinal', 'codeNumberMonth', 'codeNumberYear', 'createdProductionDateTime', 'modifiedProductionDateTime', 'productionDate', 'note'],
                'field_operators_list' => [
                    'printingMachine' => [FilterContain::class, FilterNotContain::class],
                    'designCode' => [FilterContain::class, FilterNotContain::class],
                    'productCode' => [FilterContain::class, FilterNotContain::class],
                    'quantityOrder' => [FilterEqual::class, FilterNotEqual::class],
                    'quantityStock' => [FilterEqual::class, FilterNotEqual::class],
                    'measurement' => [FilterContain::class, FilterNotContain::class],
                    'color' => [FilterContain::class, FilterNotContain::class],
                    'finishing' => [FilterContain::class, FilterNotContain::class],
                    'quantityPrinting' => [FilterEqual::class, FilterNotEqual::class],
                    'mountageSize' => [FilterContain::class, FilterNotContain::class],
                    'isUsingHotStamping' => [FilterEqual::class, FilterNotEqual::class],
                    'deliveryDate' => [FilterEqual::class, FilterNotEqual::class],
                    'printingStatus' => [FilterContain::class, FilterNotContain::class],
                    'dieCutBlade' => [FilterContain::class, FilterNotContain::class],
                    'dieCutBladeNumber' => [FilterContain::class, FilterNotContain::class],
                    'dieLineFilmNumber' => [FilterContain::class, FilterNotContain::class],
                    'layoutModelFileExtension' => [FilterContain::class, FilterNotContain::class],
                    'insitPercentage' => [FilterEqual::class, FilterNotEqual::class],
                    'quantityPaper' => [FilterEqual::class, FilterNotEqual::class],
                    'paperMountage' => [FilterEqual::class, FilterNotEqual::class],
                    'paperPlanoLength' => [FilterEqual::class, FilterNotEqual::class],
                    'paperPlanoWidth' => [FilterEqual::class, FilterNotEqual::class],
                    'cuttingLengthSize1' => [FilterEqual::class, FilterNotEqual::class],
                    'cuttingWidthSize1' => [FilterEqual::class, FilterNotEqual::class],
                    'cuttingLengthSize2' => [FilterEqual::class, FilterNotEqual::class],
                    'cuttingWidthSize2' => [FilterEqual::class, FilterNotEqual::class],
                    'paperRequirement' => [FilterContain::class, FilterNotContain::class],
                    'printingInsit' => [FilterEqual::class, FilterNotEqual::class],
                    'paperTotal' => [FilterEqual::class, FilterNotEqual::class],
                    'inkCyanPercentage' => [FilterEqual::class, FilterNotEqual::class],
                    'inkCyanWeight' => [FilterEqual::class, FilterNotEqual::class],
                    'inkMagentaPercentage' => [FilterEqual::class, FilterNotEqual::class],
                    'inkMagentaWeight' => [FilterEqual::class, FilterNotEqual::class],
                    'inkYellowPercentage' => [FilterEqual::class, FilterNotEqual::class],
                    'inkYellowWeight' => [FilterEqual::class, FilterNotEqual::class],
                    'inkBlackPercentage' => [FilterEqual::class, FilterNotEqual::class],
                    'inkBlackWeight' => [FilterEqual::class, FilterNotEqual::class],
                    'inkK1Percentage' => [FilterEqual::class, FilterNotEqual::class],
                    'inkK1Weight' => [FilterEqual::class, FilterNotEqual::class],
                    'inkK2Percentage' => [FilterEqual::class, FilterNotEqual::class],
                    'inkK2Weight' => [FilterEqual::class, FilterNotEqual::class],
                    'inkK3Percentage' => [FilterEqual::class, FilterNotEqual::class],
                    'inkK3Weight' => [FilterEqual::class, FilterNotEqual::class],
                    'inkK4Percentage' => [FilterEqual::class, FilterNotEqual::class],
                    'inkK4Weight' => [FilterEqual::class, FilterNotEqual::class],
                    'inkOpvPercentage' => [FilterEqual::class, FilterNotEqual::class],
                    'inkOpvWeight' => [FilterEqual::class, FilterNotEqual::class],
                    'inkLaminatingSize' => [FilterEqual::class, FilterNotEqual::class],
                    'inkLaminatingQuantity' => [FilterEqual::class, FilterNotEqual::class],
                    'packagingGlueQuantity' => [FilterEqual::class, FilterNotEqual::class],
                    'packagingGlueWeight' => [FilterEqual::class, FilterNotEqual::class],
                    'packagingRubberQuantity' => [FilterEqual::class, FilterNotEqual::class],
                    'packagingRubberWeight' => [FilterEqual::class, FilterNotEqual::class],
                    'packagingPaperQuantity' => [FilterEqual::class, FilterNotEqual::class],
                    'packagingPaperWeight' => [FilterEqual::class, FilterNotEqual::class],
                    'packagingBoxQuantity' => [FilterEqual::class, FilterNotEqual::class],
                    'packagingBoxWeight' => [FilterEqual::class, FilterNotEqual::class],
                    'packagingTapeLargeQuantity' => [FilterEqual::class, FilterNotEqual::class],
                    'packagingTapeLargeSize' => [FilterEqual::class, FilterNotEqual::class],
                    'packagingTapeSmallQuantity' => [FilterEqual::class, FilterNotEqual::class],
                    'packagingTapeSmallSize' => [FilterEqual::class, FilterNotEqual::class],
                    'packagingPlasticQuantity' => [FilterEqual::class, FilterNotEqual::class],
                    'packagingPlasticSize' => [FilterEqual::class, FilterNotEqual::class],
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
                'field_names' => ['printingMachine', 'designCode', 'productCode', 'quantityOrder', 'quantityStock', 'measurement', 'color', 'finishing', 'quantityPrinting', 'mountageSize', 'isUsingHotStamping', 'deliveryDate', 'printingStatus', 'dieCutBlade', 'dieCutBladeNumber', 'dieLineFilmNumber', 'layoutModelFileExtension', 'insitPercentage', 'quantityPaper', 'paperMountage', 'paperPlanoLength', 'paperPlanoWidth', 'cuttingLengthSize1', 'cuttingWidthSize1', 'cuttingLengthSize2', 'cuttingWidthSize2', 'paperRequirement', 'printingInsit', 'paperTotal', 'inkCyanPercentage', 'inkCyanWeight', 'inkMagentaPercentage', 'inkMagentaWeight', 'inkYellowPercentage', 'inkYellowWeight', 'inkBlackPercentage', 'inkBlackWeight', 'inkK1Percentage', 'inkK1Weight', 'inkK2Percentage', 'inkK2Weight', 'inkK3Percentage', 'inkK3Weight', 'inkK4Percentage', 'inkK4Weight', 'inkOpvPercentage', 'inkOpvWeight', 'inkLaminatingSize', 'inkLaminatingQuantity', 'packagingGlueQuantity', 'packagingGlueWeight', 'packagingRubberQuantity', 'packagingRubberWeight', 'packagingPaperQuantity', 'packagingPaperWeight', 'packagingBoxQuantity', 'packagingBoxWeight', 'packagingTapeLargeQuantity', 'packagingTapeLargeSize', 'packagingTapeSmallQuantity', 'packagingTapeSmallSize', 'packagingPlasticQuantity', 'packagingPlasticSize', 'isCanceled', 'isRead', 'codeNumberOrdinal', 'codeNumberMonth', 'codeNumberYear', 'createdProductionDateTime', 'modifiedProductionDateTime', 'productionDate', 'note'],
                'field_operators_list' => [
                    'printingMachine' => [SortAscending::class, SortDescending::class],
                    'designCode' => [SortAscending::class, SortDescending::class],
                    'productCode' => [SortAscending::class, SortDescending::class],
                    'quantityOrder' => [SortAscending::class, SortDescending::class],
                    'quantityStock' => [SortAscending::class, SortDescending::class],
                    'measurement' => [SortAscending::class, SortDescending::class],
                    'color' => [SortAscending::class, SortDescending::class],
                    'finishing' => [SortAscending::class, SortDescending::class],
                    'quantityPrinting' => [SortAscending::class, SortDescending::class],
                    'mountageSize' => [SortAscending::class, SortDescending::class],
                    'isUsingHotStamping' => [SortAscending::class, SortDescending::class],
                    'deliveryDate' => [SortAscending::class, SortDescending::class],
                    'printingStatus' => [SortAscending::class, SortDescending::class],
                    'dieCutBlade' => [SortAscending::class, SortDescending::class],
                    'dieCutBladeNumber' => [SortAscending::class, SortDescending::class],
                    'dieLineFilmNumber' => [SortAscending::class, SortDescending::class],
                    'layoutModelFileExtension' => [SortAscending::class, SortDescending::class],
                    'insitPercentage' => [SortAscending::class, SortDescending::class],
                    'quantityPaper' => [SortAscending::class, SortDescending::class],
                    'paperMountage' => [SortAscending::class, SortDescending::class],
                    'paperPlanoLength' => [SortAscending::class, SortDescending::class],
                    'paperPlanoWidth' => [SortAscending::class, SortDescending::class],
                    'cuttingLengthSize1' => [SortAscending::class, SortDescending::class],
                    'cuttingWidthSize1' => [SortAscending::class, SortDescending::class],
                    'cuttingLengthSize2' => [SortAscending::class, SortDescending::class],
                    'cuttingWidthSize2' => [SortAscending::class, SortDescending::class],
                    'paperRequirement' => [SortAscending::class, SortDescending::class],
                    'printingInsit' => [SortAscending::class, SortDescending::class],
                    'paperTotal' => [SortAscending::class, SortDescending::class],
                    'inkCyanPercentage' => [SortAscending::class, SortDescending::class],
                    'inkCyanWeight' => [SortAscending::class, SortDescending::class],
                    'inkMagentaPercentage' => [SortAscending::class, SortDescending::class],
                    'inkMagentaWeight' => [SortAscending::class, SortDescending::class],
                    'inkYellowPercentage' => [SortAscending::class, SortDescending::class],
                    'inkYellowWeight' => [SortAscending::class, SortDescending::class],
                    'inkBlackPercentage' => [SortAscending::class, SortDescending::class],
                    'inkBlackWeight' => [SortAscending::class, SortDescending::class],
                    'inkK1Percentage' => [SortAscending::class, SortDescending::class],
                    'inkK1Weight' => [SortAscending::class, SortDescending::class],
                    'inkK2Percentage' => [SortAscending::class, SortDescending::class],
                    'inkK2Weight' => [SortAscending::class, SortDescending::class],
                    'inkK3Percentage' => [SortAscending::class, SortDescending::class],
                    'inkK3Weight' => [SortAscending::class, SortDescending::class],
                    'inkK4Percentage' => [SortAscending::class, SortDescending::class],
                    'inkK4Weight' => [SortAscending::class, SortDescending::class],
                    'inkOpvPercentage' => [SortAscending::class, SortDescending::class],
                    'inkOpvWeight' => [SortAscending::class, SortDescending::class],
                    'inkLaminatingSize' => [SortAscending::class, SortDescending::class],
                    'inkLaminatingQuantity' => [SortAscending::class, SortDescending::class],
                    'packagingGlueQuantity' => [SortAscending::class, SortDescending::class],
                    'packagingGlueWeight' => [SortAscending::class, SortDescending::class],
                    'packagingRubberQuantity' => [SortAscending::class, SortDescending::class],
                    'packagingRubberWeight' => [SortAscending::class, SortDescending::class],
                    'packagingPaperQuantity' => [SortAscending::class, SortDescending::class],
                    'packagingPaperWeight' => [SortAscending::class, SortDescending::class],
                    'packagingBoxQuantity' => [SortAscending::class, SortDescending::class],
                    'packagingBoxWeight' => [SortAscending::class, SortDescending::class],
                    'packagingTapeLargeQuantity' => [SortAscending::class, SortDescending::class],
                    'packagingTapeLargeSize' => [SortAscending::class, SortDescending::class],
                    'packagingTapeSmallQuantity' => [SortAscending::class, SortDescending::class],
                    'packagingTapeSmallSize' => [SortAscending::class, SortDescending::class],
                    'packagingPlasticQuantity' => [SortAscending::class, SortDescending::class],
                    'packagingPlasticSize' => [SortAscending::class, SortDescending::class],
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
