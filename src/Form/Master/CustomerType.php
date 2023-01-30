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
//            ->add('code')
            ->add('company')
            ->add('name')
            ->add('addressInvoice', null, ['label' => 'Alamat Penagihan'])
            ->add('addressDelivery', null, ['label' => 'Alamat Kirim'])
            ->add('phone')
            ->add('email')
            ->add('taxNumber', null, ['label' => 'NPWP'])
            ->add('paymentTerm', null, ['label' => 'TOP (hari)'])
            ->add('isBondedZone', null, ['label' => 'Berikat 070?'])
            ->add('account', null, ['choice_label' => 'name'])
            ->add('note')
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
