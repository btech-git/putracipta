<?php

namespace App\Form\Stock;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedNumberType;
use App\Entity\Master\Paper;
use App\Entity\Stock\InventoryRequestPaperDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InventoryRequestPaperDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', FormattedNumberType::class, ['decimals' => 0])
            ->add('memo')
            ->add('isCanceled')
            ->add('paper', EntityHiddenType::class, array('class' => Paper::class))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InventoryRequestPaperDetail::class,
        ]);
    }
}
