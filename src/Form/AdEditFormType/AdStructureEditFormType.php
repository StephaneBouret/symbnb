<?php

namespace App\Form\AdEditFormType;

use App\Entity\Ad;
use App\Entity\Type;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class AdStructureEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', EntityType::class, [
                'class' => Type::class,
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
                'choice_label' => 'name',
                'expanded' => false,
                'multiple' => false,
                'attr' => [
                    'class' => 's18j14tt'
                ],
            ])
            ->add('rooms', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'input-stepper-item',
                    'min' => 1
                ],
            ])
            ->add('beds', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'input-stepper-item',
                    'min' => 1
                ],
            ])
            ->add('price', MoneyType::class, [
                'divisor' => 100,
                'currency' => false,
                'attr' => [
                    'class' => 'input-price-edit',
                    'min' => 10
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // $resolver->setRequired('types');
        // $resolver->setAllowedTypes('types', 'iterable');
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
