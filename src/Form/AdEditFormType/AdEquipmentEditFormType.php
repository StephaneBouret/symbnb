<?php

namespace App\Form\AdEditFormType;

use App\Entity\Ad;
use App\Entity\Equipment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdEquipmentEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $criterias = $options['criterias'];

        foreach ($criterias as $criteria) {
            $builder->add('equipment_' . $criteria->getId(), EntityType::class, [
                'class' => Equipment::class,
                'choices' => $criteria->getEquipment(),
                'multiple' => true,
                'expanded' => true,
                'choice_label' => 'name',
                'choice_attr' => function($choice, $key, $value) {
                    return ['data-id' => $choice->getId()];
                },
                'label' => $criteria->getName(),
                'mapped' => false, // This prevents the form from trying to map to the Ad entity directly
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
            'criterias' => null,
        ]);
    }
}
