<?php

namespace App\Form\Sale;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedNumberType;
use App\Entity\Sale\SaleOrderDetail;
use App\Entity\Sale\DeliveryDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeliveryDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', FormattedNumberType::class, ['decimals' => 0])
            ->add('isCanceled')
            ->add('lotNumber')
            ->add('linePo')
            ->add('packaging')
            ->add('fscCode', ChoiceType::class, ['choices' => [
                '' => '',
                'A' => 'A',
                'B' => 'B',
                'C' => 'C',
                'D' => 'D',
            ]])
            ->add('saleOrderDetail', EntityHiddenType::class, ['class' => SaleOrderDetail::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DeliveryDetail::class,
        ]);
    }
}
