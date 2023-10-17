<?php

namespace App\Form\Sale;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Customer;
use App\Entity\Sale\SaleInvoiceDetail;
use App\Entity\Sale\SaleInvoiceHeader;
use App\Repository\Admin\LiteralConfigRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaleInvoiceHeaderType extends AbstractType
{
    private LiteralConfigRepository $literalConfigRepository;

    public function __construct(LiteralConfigRepository $literalConfigRepository)
    {
        $this->literalConfigRepository = $literalConfigRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $vatPercentage = $this->literalConfigRepository->findLiteralValue('vatPercentage');
//        $serviceTaxPercentage = $this->literalConfigRepository->findLiteralValue('serviceTaxPercentage');
        $builder
            ->add('invoiceTaxCodeNumber')
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('invoiceTaxDate', null, ['widget' => 'single_text'])
            ->add('note')
            ->add('discountValueType', ChoiceType::class, ['choices' => [
                'Percentage' => SaleInvoiceHeader::DISCOUNT_VALUE_TYPE_PERCENTAGE,
                'Nominal' => SaleInvoiceHeader::DISCOUNT_VALUE_TYPE_NOMINAL,
            ]])
            ->add('discountValue')
            ->add('taxMode', ChoiceType::class, ['choices' => [
                '0%' => SaleInvoiceHeader::TAX_MODE_NON_TAX,
                "{$vatPercentage}%" => SaleInvoiceHeader::TAX_MODE_TAX_EXCLUSION,
            ]])
//            ->add('serviceTaxMode', ChoiceType::class, ['choices' => [
//                '0.00%' => SaleInvoiceHeader::SERVICE_TAX_MODE_NON_TAX,
//                "{$serviceTaxPercentage}%" => SaleInvoiceHeader::SERVICE_TAX_MODE_TAX,
//            ]])
            ->add('customer', EntityHiddenType::class, ['class' => Customer::class])
            ->add('saleInvoiceDetails', CollectionType::class, [
                'entry_type' => SaleInvoiceDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new SaleInvoiceDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SaleInvoiceHeader::class,
        ]);
    }
}
