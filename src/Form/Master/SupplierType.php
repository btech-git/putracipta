<?php

namespace App\Form\Master;

use App\Entity\Master\Supplier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SupplierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//            ->add('code')
            ->add('company')
            ->add('name', null, ['label' => 'PIC'])
            ->add('address', null, ['label' => 'Alamat (**Tekan ENTER untuk > 1 baris)'])
            ->add('phone')
            ->add('fax')
            ->add('email')
            ->add('taxNumber', null, ['label' => 'NPWP'])
            ->add('paymentTerm', null, ['label' => 'TOP (hari)'])
            ->add('certification', null, ['label' => 'Category'])
            ->add('currency', null, ['choice_label' => 'name'])
            ->add('account', null, ['choice_label' => 'name'])
            ->add('note')
            ->add('isInactive')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Supplier::class,
        ]);
    }
}
