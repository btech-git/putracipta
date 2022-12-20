<?php

namespace App\Form\Master;

use App\Entity\Master\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code')
            ->add('company')
            ->add('name')
            ->add('addressInvoice')
            ->add('addressDelivery')
            ->add('phone')
            ->add('email')
            ->add('taxNumber')
            ->add('paymentTerm')
            ->add('account', null, ['choice_label' => 'name'])
            ->add('note')
            ->add('isBondedZone')
            ->add('isInactive')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
