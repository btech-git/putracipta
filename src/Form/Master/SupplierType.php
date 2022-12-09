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
            ->add('code')
            ->add('company')
            ->add('name')
            ->add('address')
            ->add('phone')
            ->add('fax')
            ->add('email')
            ->add('taxNumber')
            ->add('paymentTerm')
            ->add('certification')
            ->add('account')
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
