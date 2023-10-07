<?php

namespace App\Form\Purchase;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedNumberType;
use App\Entity\Master\Paper;
use App\Entity\Purchase\PurchaseRequestPaperDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseRequestPaperDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', FormattedNumberType::class, ['decimals' => 0])
            ->add('usageDate', null, ['widget' => 'single_text'])
            ->add('memo')
            ->add('isCanceled')
            ->add('paper', EntityHiddenType::class, array('class' => Paper::class))
            ->add('unit', null, ['choice_label' => 'name'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseRequestPaperDetail::class,
        ]);
    }
}
