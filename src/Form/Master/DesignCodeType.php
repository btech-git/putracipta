<?php

namespace App\Form\Master;

use App\Entity\Master\DesignCode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DesignCodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customer', null, [
                'choice_label' => 'idNameLiteral',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false")
                            ->addOrderBy('e.company', 'ASC');
                },
            ])
            ->add('name', null, ['label' => 'Nama Produk'])
            ->add('code', null, ['label' => 'Kode Produk'])
            ->add('variant', null, ['label' => 'Varian'])
            ->add('version', null, ['label' => 'Revisi'])
            ->add('color', null, ['label' => 'Jlh Warna'])
            ->add('pantone', null, ['label' => 'Separasi Warna'])
            ->add('colorSpecial1', null, ['label' => 'Warna Khusus 1'])
            ->add('colorSpecial2', null, ['label' => 'Warna Khusus 2'])
            ->add('colorSpecial3', null, ['label' => 'Warna Khusus 3'])
            ->add('colorSpecial4', null, ['label' => 'Warna Khusus 4'])
            ->add('coating', null, ['label' => 'Coating'])
            ->add('printingUpQuantity', null, ['label' => 'Jumlah Mata (Up/s)'])
            ->add('printingKrisSize', null, ['label' => 'Uk. Kris Cetak (cm)'])
            ->add('paperCuttingLength', null, ['label' => false])
            ->add('paperCuttingWidth', null, ['label' => false])
            ->add('paperMountage', null, ['label' => 'Mountage Kertas (lbr/plano)'])
            ->add('inkCyanPercentage', null, ['label' => 'Cyan (%)'])
            ->add('inkMagentaPercentage', null, ['label' => 'Magenta (%)'])
            ->add('inkYellowPercentage', null, ['label' => 'Yellow (%)'])
            ->add('inkBlackPercentage', null, ['label' => 'Black (%)'])
            ->add('inkOpvPercentage', null, ['label' => 'OPV / WB / UV (%)'])
            ->add('inkK1Percentage', null, ['label' => 'Khusus 1 (%)'])
            ->add('inkK2Percentage', null, ['label' => 'Khusus 2 (%)'])
            ->add('inkK3Percentage', null, ['label' => 'Khusus 3 (%)'])
            ->add('inkK4Percentage', null, ['label' => 'Khusus 4 (%)'])
            ->add('packagingGlueQuantity', null, ['label' => 'Lem (cm/pcs)'])
            ->add('packagingRubberQuantity', null, ['label' => 'Karet (pcs/ikat)'])
            ->add('packagingPaperQuantity', null, ['label' => 'Kertas Packing (pcs/pack)'])
            ->add('packagingBoxQuantity', null, ['label' => 'Dus (pcs/dus)'])
            ->add('packagingTapeLargeQuantity', null, ['label' => 'Lakban Besar (cm/pack)'])
            ->add('packagingTapeSmallQuantity', null, ['label' => 'Lakban Kecil (cm/pack)'])
            ->add('packagingPlasticQuantity', null, ['label' => 'Plastik (cm2/pack)'])
            ->add('insitPrintingPercentage', null, ['label' => '% Insit Cetak'])
            ->add('insitSortingPercentage', null, ['label' => '% Insit Sortir'])
            ->add('diecutKnife', null, [
                'choice_label' => 'codeNumber',
                'label' => 'Pisau Diecut',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false");
                },
            ])
            ->add('dielineMillar', null, [
                'choice_label' => 'codeNumber',
                'label' => 'Millar',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false");
                },
            ])
            ->add('status', ChoiceType::class, ['label' => 'Status', 'choices' => [
                'FA' => DesignCode::STATUS_FA,
                'NA' => DesignCode::STATUS_NA,
            ]])
            ->add('note')
            ->add('isInactive')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DesignCode::class,
        ]);
    }
}
