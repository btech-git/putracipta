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
                'choice_label' => 'company',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                            ->andWhere("e.isInactive = false")
                            ->addOrderBy('e.company', 'ASC');
                },
            ])
            ->add('code', null, ['label' => 'Kode Produk'])
            ->add('name', null, ['label' => 'Nama'])
            ->add('variant', null, ['label' => 'Varian'])
            ->add('version', null, ['label' => 'Versi'])
            ->add('color', null, ['label' => 'Jlh Warna'])
            ->add('pantone', null, ['label' => 'Separasi Warna'])
            ->add('colorSpecial1', null, ['label' => 'Warna Khusus 1'])
            ->add('colorSpecial2', null, ['label' => 'Warna Khusus 2'])
            ->add('colorSpecial3', null, ['label' => 'Warna Khusus 3'])
            ->add('colorSpecial4', null, ['label' => 'Warna Khusus 4'])
            ->add('coating', null, ['label' => 'Coating'])
            ->add('printingUpQuantity', null, ['label' => 'Jumlah Mata (Up/s)'])
            ->add('printingKrisSize', null, ['label' => 'Uk. Kris Cetak (cm)'])
            ->add('paperCuttingSize', null, ['label' => 'Uk. Ptg Kertas (cm)'])
            ->add('paperMountage', null, ['label' => 'Mountage Kertas (lbr/plano)'])
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
