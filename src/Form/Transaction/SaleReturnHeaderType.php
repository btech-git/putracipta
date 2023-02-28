<?php

namespace App\Form\Transaction;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Transaction\DeliveryHeader;
use App\Entity\Transaction\SaleReturnDetail;
use App\Entity\Transaction\SaleReturnHeader;
use App\Repository\Admin\LiteralConfigRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaleReturnHeaderType extends AbstractType
{
    private LiteralConfigRepository $literalConfigRepository;

    public function __construct(LiteralConfigRepository $literalConfigRepository)
    {
        $this->literalConfigRepository = $literalConfigRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $vatPercentage = $this->literalConfigRepository->findLiteralValue('vatPercentage');
        $builder
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('warehouse', null, ['choice_label' => 'name'])
            ->add('taxMode', ChoiceType::class, ['choices' => [
                '0%' => SaleReturnHeader::TAX_MODE_NON_TAX,
                "{$vatPercentage}%" => SaleReturnHeader::TAX_MODE_TAX_EXCLUSION,
            ]])
            ->add('deliveryHeader', EntityHiddenType::class, ['class' => DeliveryHeader::class])
            ->add('saleReturnDetails', CollectionType::class, [
                'entry_type' => SaleReturnDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new SaleReturnDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SaleReturnHeader::class,
        ]);
    }
}
