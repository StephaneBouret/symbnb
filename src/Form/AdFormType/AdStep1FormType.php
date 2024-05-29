<?php

namespace App\Form\AdFormType;

use App\Entity\Ad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdStep1FormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rooms', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'input-stepper-item',
                    'min' => 1
                ],
                'data' => 1
            ])
            ->add('beds', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'input-stepper-item',
                    'min' => 1
                ],
                'data' => 1
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
