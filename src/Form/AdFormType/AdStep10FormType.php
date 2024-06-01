<?php

namespace App\Form\AdFormType;

use App\Entity\Ad;
use App\Entity\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdStep10FormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $types = $options['types'];

        $builder->add('type', EntityType::class, [
            'class' => Type::class,
            'choices' => $types,
            'choice_label' => 'name',
            'expanded' => true,
            'multiple' => false,
            'label' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('types');
        $resolver->setAllowedTypes('types', 'iterable');
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
