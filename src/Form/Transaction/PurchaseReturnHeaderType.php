<?php

namespace App\Form\Transaction;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Transaction\PurchaseReturnDetail;
use App\Entity\Transaction\PurchaseReturnHeader;
use App\Entity\Transaction\ReceiveHeader;
use App\Repository\Admin\LiteralConfigRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseReturnHeaderType extends AbstractType
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
                '0%' => PurchaseReturnHeader::TAX_MODE_NON_TAX,
                "{$vatPercentage}%" => PurchaseReturnHeader::TAX_MODE_TAX_EXCLUSION,
            ]])
            ->add('receiveHeader', EntityHiddenType::class, ['class' => ReceiveHeader::class])
            ->add('purchaseReturnDetails', CollectionType::class, [
                'entry_type' => PurchaseReturnDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new PurchaseReturnDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseReturnHeader::class,
        ]);
    }
}
