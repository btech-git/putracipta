<?php

namespace App\Form\Production;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Material;
use App\Entity\Production\MasterOrder;
use App\Entity\Production\WorkOrder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('orderStatus', ChoiceType::class, ['choices' => [
                'New' => WorkOrder::STATUS_NEW,
                'Repeat' => WorkOrder::STATUS_REPEAT,
            ]])
            ->add('color')
            ->add('orderUp')
            ->add('insit')
            ->add('printingKrisLength')
            ->add('printingKrisWidth')
            ->add('planoSizeLength')
            ->add('planoSizeWidth')
            ->add('planoQuantity')
            ->add('cuttingLength1')
            ->add('cuttingWidth1')
            ->add('cuttingLength2')
            ->add('cuttingWidth2')
            ->add('cuttingQuantity')
            ->add('cuttingInsit')
            ->add('packingMaterialBox')
            ->add('packingMaterialPaper')
            ->add('planoConversion')
            ->add('note')
            ->add('productionDate', null, ['widget' => 'single_text'])
            ->add('masterOrder', EntityHiddenType::class, ['class' => MasterOrder::class])
            ->add('material', EntityHiddenType::class, array('class' => Material::class))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkOrder::class,
        ]);
    }
}
