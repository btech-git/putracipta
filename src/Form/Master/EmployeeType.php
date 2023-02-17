<?php

namespace App\Form\Master;

use App\Entity\Master\Employee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('email')
            ->add('address')
            ->add('phone')
            ->add('birthDate', null, ['label' => 'Tanggal Lahir', 'widget' => 'single_text'])
            ->add('birthPlace', null, ['label' => 'Tempat Lahir'])
            ->add('identityNumber', null, ['label' => 'KTP'])
            ->add('division', ChoiceType::class, ['choices' => [
                'Sales' => Employee::DIVISION_SALES,
                'Marketing' => Employee::DIVISION_MARKETING,
                'Transportation' => Employee::DIVISION_TRANSPORTATION,
                'Warehouse' => Employee::DIVISION_WAREHOUSE,
                'Purchasing' => Employee::DIVISION_PURCHASING,
            ]])
            ->add('startDate', null, ['label' => 'Tanggal Mulai', 'widget' => 'single_text'])
            ->add('note')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
        ]);
    }
}
