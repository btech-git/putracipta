<?php

namespace App\Form\Transaction;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Customer;
use App\Entity\Master\Employee;
use App\Entity\Transaction\DeliveryDetail;
use App\Entity\Transaction\DeliveryHeader;
use App\Repository\Master\CustomerRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeliveryHeaderType extends AbstractType
{
    private CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transactionDate', null, ['widget' => 'single_text'])
            ->add('warehouse', null, ['choice_label' => 'name'])
            ->add('employee', null, [
                'choice_label' => 'name',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('e')
                        ->andWhere("e.division = '" . Employee::DIVISION_TRANSPORTATION . "'")
                        ->andWhere("e.isInactive = false");
                },
            ])
            ->add('transportation', null, ['choice_label' => 'nameAndPlateNumber'])
            ->add('note')
//            ->add('addressDelivery', TextType::class)
            ->add('customer', EntityHiddenType::class, ['class' => Customer::class])
            ->add('deliveryDetails', CollectionType::class, [
                'entry_type' => DeliveryDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new DeliveryDetail(),
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DeliveryHeader::class,
        ]);
    }
}
