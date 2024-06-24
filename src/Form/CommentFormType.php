<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('rating', IntegerType::class, [
            //     'label' => false,
            //     'attr' => [
            //         'placeholder' => 'Merci de nous laisser votre note (entre 1 et 5)',
            //         'min' => 1,
            //         'max' => 5,
            //         'step' => 1,
            //     ]
            // ])
            ->add('rating', HiddenType::class, [
                'label' => false,
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Votre commentaire :',
                'attr' => [
                    'placeholder' => 'Vos remarques sur votre séjour aideront les prochains voyageurs à mieux profiter de leur expérience',
                    'class' => 'description-textarea',
                    'rows' => 4,
                    'autocapitalize' => 'sentences'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
