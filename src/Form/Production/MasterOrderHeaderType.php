<?php

namespace App\Form\Production;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedNumberType;
use App\Entity\Master\DesignCode;
use App\Entity\Master\DiecutKnife;
use App\Entity\Master\DielineMillar;
use App\Entity\Master\Paper;
use App\Entity\Production\MasterOrderCheckSheetDetail;
use App\Entity\Production\MasterOrderDistributionDetail;
use App\Entity\Production\MasterOrderHeader;
use App\Entity\Production\MasterOrderProductDetail;
use App\Entity\Production\MasterOrderProcessDetail;
use App\Entity\Production\ProductDevelopment;
use App\Entity\Transaction\PurchaseOrderPaperHeader;
use App\Repository\Master\CustomerRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class MasterOrderHeaderType extends AbstractType
{
    private CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('machinePrinting', null, [
                'choice_label' => 'name',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false");
                },
            ])
            ->add('customer', null, [
                'choice_label' => 'company',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false")
                            ->addOrderBy('e.company', 'ASC');
                },
            ])
            ->add('hotStamping')
            ->add('glossiness')
//            ->add('weightPerPiece')
            ->add('inkHotStampingSize')
            ->add('color')
            ->add('pantone')
            ->add('finishing')
            ->add('quantityPrinting', FormattedNumberType::class, ['decimals' => 0])
            ->add('quantityPrinting2', FormattedNumberType::class, ['decimals' => 0])
            ->add('mountageSize')
            ->add('orderType', ChoiceType::class, ['multiple' => false, 'expanded' => false, 'choices' => [
                'MO Biasa' => MasterOrderHeader::ORDER_TYPE_REGULAR,
                'MO Kekurangan Cetak' => MasterOrderHeader::ORDER_TYPE_PRINTING_SHORTAGE,
                'MO Kekurangan Retur' => MasterOrderHeader::ORDER_TYPE_RETURN_SHORTAGE,
                'MO Sambungan' => MasterOrderHeader::ORDER_TYPE_EXTENSION,
            ]])
            ->add('printingStatus', ChoiceType::class, ['multiple' => true, 'expanded' => true, 'choices' => [
                'Proof Print' => MasterOrderHeader::PRINTING_STATUS_PROOF_PRINT,
                'New Order' => MasterOrderHeader::PRINTING_STATUS_NEW_ORDER,
                'Repeat Order' => MasterOrderHeader::PRINTING_STATUS_REPEAT_ORDER,
                'Revisi Design' => MasterOrderHeader::PRINTING_STATUS_REVISE_DESIGN,
            ]])
            ->add('dieCutBlade', ChoiceType::class, ['choices' => [
                'Baru' => MasterOrderHeader::DIECUT_BLADE_NEW,
                'Lama' => MasterOrderHeader::DIECUT_BLADE_OLD,
                'Revisi' => MasterOrderHeader::DIECUT_BLADE_REVISION,
            ]])
            ->add('insitPrintingPercentage')
            ->add('insitSortingPercentage')
            ->add('paperMountage')
            ->add('cuttingLengthSize1')
            ->add('cuttingWidthSize1')
            ->add('cuttingLengthSize2')
            ->add('cuttingWidthSize2')
            ->add('inkCyanPercentage')
            ->add('inkMagentaPercentage')
            ->add('inkYellowPercentage')
            ->add('inkBlackPercentage')
            ->add('inkK1Color')
            ->add('inkK1Percentage')
            ->add('inkK2Color')
            ->add('inkK2Percentage')
            ->add('inkK3Color')
            ->add('inkK3Percentage')
            ->add('inkK4Color')
            ->add('inkK4Percentage')
            ->add('inkOpvPercentage')
            ->add('inkLaminatingSize')
            ->add('quantityStockPaper')
            ->add('packagingGlueQuantity', FormattedNumberType::class, ['decimals' => 0])
            ->add('packagingRubberQuantity', FormattedNumberType::class, ['decimals' => 0])
            ->add('packagingPaperQuantity', FormattedNumberType::class, ['decimals' => 0])
            ->add('packagingBoxQuantity', FormattedNumberType::class, ['decimals' => 0])
            ->add('packagingTapeLargeQuantity', FormattedNumberType::class, ['decimals' => 0])
            ->add('packagingTapeSmallQuantity', FormattedNumberType::class, ['decimals' => 0])
            ->add('packagingPlasticQuantity', FormattedNumberType::class, ['decimals' => 0])
            ->add('note')
            ->add('purchaseOrderPaperHeader', EntityHiddenType::class, ['class' => PurchaseOrderPaperHeader::class])
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('productDevelopment', EntityHiddenType::class, ['class' => ProductDevelopment::class])
            ->add('diecutKnife', EntityHiddenType::class, ['class' => DiecutKnife::class])
            ->add('designCode', EntityHiddenType::class, ['class' => DesignCode::class])
            ->add('dielineMillar', EntityHiddenType::class, ['class' => DielineMillar::class])
            ->add('paper', EntityHiddenType::class, array('class' => Paper::class))
            ->add('masterOrderProcessDetails', CollectionType::class, [
                'entry_type' => MasterOrderProcessDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new MasterOrderProcessDetail(),
                'label' => false,
            ])
            ->add('masterOrderDistributionDetails', CollectionType::class, [
                'entry_type' => MasterOrderDistributionDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new MasterOrderDistributionDetail(),
                'label' => false,
            ])
            ->add('masterOrderCheckSheetDetails', CollectionType::class, [
                'entry_type' => MasterOrderCheckSheetDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new MasterOrderCheckSheetDetail(),
                'label' => false,
            ])
            ->add('transactionFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5120k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'application/pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPEG or PNG',
                    ])
                ],
            ])
            ->add('masterOrderProductDetails', CollectionType::class, [
                'entry_type' => MasterOrderProductDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new MasterOrderProductDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MasterOrderHeader::class,
        ]);
    }
}
