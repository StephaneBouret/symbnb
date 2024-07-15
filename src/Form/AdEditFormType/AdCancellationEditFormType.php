<?php

namespace App\Form\AdEditFormType;

use App\Entity\Ad;
use App\Entity\Cancellation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdCancellationEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('cancellation', EntityType::class, [
            'class' => Cancellation::class,
            'choice_label' => function (Cancellation $cancellation) {
                return $cancellation->getName() . ' - ' . $cancellation->getDescription();
            },
            'multiple' => false,
            'expanded' => true,
            'required' => true,
            'placeholder' => null,
            'data' => $options['data']->getCancellation() ?? null,
            'label' => false,
            'attr' => ['class' => 'hw98sls']
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ad::class
        ]);
    }
}
