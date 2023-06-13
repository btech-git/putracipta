<?php

namespace App\Form\Production;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Customer;
use App\Entity\Master\DiecutKnife;
use App\Entity\Master\Paper;
use App\Entity\Production\MasterOrderCheckSheetDetail;
use App\Entity\Production\MasterOrderDistributionDetail;
use App\Entity\Production\MasterOrderHeader;
use App\Entity\Production\MasterOrderProductDetail;
use App\Entity\Production\MasterOrderProcessDetail;
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
            ->add('machinePrinting', null, ['choice_label' => 'name'])
            ->add('hotStamping')
            ->add('glossiness')
            ->add('weightPerPiece')
            ->add('inkHotStampingSize')
            ->add('color')
            ->add('pantone')
            ->add('finishing')
            ->add('quantityPrinting')
            ->add('quantityPrinting2')
            ->add('mountageSizeLength')
            ->add('mountageSizeWidth')
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
            ->add('printingStatusData')
            ->add('dieCutBlade', ChoiceType::class, ['choices' => [
                'Baru' => MasterOrderHeader::DIECUT_BLADE_NEW,
                'Lama' => MasterOrderHeader::DIECUT_BLADE_OLD,
                'Revisi' => MasterOrderHeader::DIECUT_BLADE_REVISION,
            ]])
            ->add('dieCutBladeData')
            ->add('dieLineFilmNumber')
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
            ->add('packagingGlueQuantity')
            ->add('packagingRubberQuantity')
            ->add('packagingPaperQuantity')
            ->add('packagingBoxQuantity')
            ->add('packagingTapeLargeQuantity')
            ->add('packagingTapeSmallQuantity')
            ->add('packagingPlasticQuantity')
            ->add('note')
            ->add('purchaseOrderPaperHeader', EntityHiddenType::class, ['class' => PurchaseOrderPaperHeader::class])
            ->add('deliveryDate', null, ['widget' => 'single_text'])
            ->add('productionDate', null, ['widget' => 'single_text'])
            ->add('customer', EntityHiddenType::class, ['class' => Customer::class])
            ->add('diecutKnife', EntityHiddenType::class, ['class' => DiecutKnife::class])
            ->add('paper', EntityHiddenType::class, array('class' => Paper::class))
//            ->add('workOrderDistribution', ChoiceType::class, ['multiple' => true, 'expanded' => true, 'choices' => [
//                'WO Prepress' => MasterOrderHeader::WORK_ORDER_DISTRIBUTION_PREPRESS,
//                'WO Colour Mixing' => MasterOrderHeader::WORK_ORDER_DISTRIBUTION_COLOUR_MIXING,
//                'WO Potong Bahan' => MasterOrderHeader::WORK_ORDER_DISTRIBUTION_CUTTING_MATERIAL,
//                'WO Printing' => MasterOrderHeader::WORK_ORDER_DISTRIBUTION_PRINTING,
//                'WO Diecut PON/BOBST' => MasterOrderHeader::WORK_ORDER_DISTRIBUTION_DIECUT,
//                'WO Potong Jadi' => MasterOrderHeader::WORK_ORDER_DISTRIBUTION_CUTTING_FINISHING,
//                'WO Varnish/Laminating' => MasterOrderHeader::WORK_ORDER_DISTRIBUTION_VARNISH,
//                'WO Screen Printing' => MasterOrderHeader::WORK_ORDER_DISTRIBUTION_SCREEN_PRINTING,
//                'WO Folder Glueing' => MasterOrderHeader::WORK_ORDER_DISTRIBUTION_FOLDER_GLUEING,
//                'WO Hot Stamp' => MasterOrderHeader::WORK_ORDER_DISTRIBUTION_HOT_STAMP,
//                'WO Mesin Jahit Kawat (Stitching)' => MasterOrderHeader::WORK_ORDER_DISTRIBUTION_STITCHING,
//                'WO Mesin Lipat' => MasterOrderHeader::WORK_ORDER_DISTRIBUTION_FOLDING,
//                'WO Finishing' => MasterOrderHeader::WORK_ORDER_DISTRIBUTION_FINISHING,
//                'WO Packing' => MasterOrderHeader::WORK_ORDER_DISTRIBUTION_PACKING,
//                'WO Sortir' => MasterOrderHeader::WORK_ORDER_DISTRIBUTION_SORTING,
//            ]])
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
