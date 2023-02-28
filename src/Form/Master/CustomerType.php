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
            ->add('name', null, ['label' => 'PIC'])
            ->add('addressInvoice', null, ['label' => 'Alamat Penagihan (**Tekan ENTER untuk > 1 baris)'])
            ->add('addressDelivery', null, ['label' => 'Alamat Kirim 1 (**Tekan ENTER untuk > 1 baris)'])
            ->add('addressDelivery2', null, ['label' => 'Alamat Kirim 2 (**Tekan ENTER untuk > 1 baris)'])
            ->add('addressDelivery3', null, ['label' => 'Alamat Kirim 3 (**Tekan ENTER untuk > 1 baris)'])
            ->add('addressDelivery4', null, ['label' => 'Alamat Kirim 4 (**Tekan ENTER untuk > 1 baris)'])
            ->add('addressDelivery5', null, ['label' => 'Alamat Kirim 5 (**Tekan ENTER untuk > 1 baris)'])
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
