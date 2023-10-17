<?php

namespace App\Form\Master;

use App\Entity\Master\Paper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaperType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('length', null, ['label' => 'Panjang'])
            ->add('width', null, ['label' => 'Lebar'])
            ->add('weight', null, ['label' => 'Berat (GSM)'])
            ->add('unit', null, ['choice_label' => 'name', 'label' => 'Satuan'])
            ->add('note')
            ->add('isInactive', null, ['label' => 'Inactive'])
            ->add('pricingMode', ChoiceType::class, ['label' => 'Metode Input Harga', 'choices' => [
                'Asosiasi' => Paper::PRICING_MODE_ASSOCIATION,
                'KG' => Paper::PRICING_MODE_WEIGHT,
                'Satuan' => Paper::PRICING_MODE_UNIT,
            ]])
            ->add('type', ChoiceType::class, ['choices' => [
                '000' => Paper::TYPE_NON_FSC,
                'FSC' => Paper::TYPE_FSC,
            ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Paper::class,
        ]);
    }
}
