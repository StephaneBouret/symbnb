<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UpdateUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Votre prénom :',
                'constraints' => new Length([
                    'min' => 2,
                    'max' => 30,
                    'minMessage' => 'Votre prénom doit comporter au moins {{ limit }} caractères',
                    'maxMessage' => 'Votre prénom ne peut excéder {{ limit }} caractères',
                ]),
                'attr' => [
                    'placeholder' => 'Merci de saisir votre prénom'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Votre nom :',
                'attr' => [
                    'placeholder' => 'Merci de saisir votre nom'
                ],
                'constraints' => new Length([
                    'min' => 2,
                    'minMessage' => 'Votre nom doit comporter au moins {{ limit }} caractères'
                ])
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre email :',
                'disabled' => true,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Merci de saisir votre adresse email'
                ],
                'constraints' => new Email()
            ])
            ->add('adress', TextType::class, [
                'label' => 'Votre adresse :',
                'label_attr' => [
                    'class' => 'lh-label fw-medium'
                ],
                'attr' => [
                    'placeholder' => 'Merci de saisir votre adresse'
                ],
                'constraints' => new NotBlank([
                    'message' => 'Merci d\'indiquer votre adresse',
                ])
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Votre code postal :',
                'label_attr' => [
                    'class' => 'lh-label fw-medium'
                ],
                'attr' => [
                    'placeholder' => 'Merci de saisir votre code postal'
                ],
                'constraints' => new NotBlank([
                    'message' => 'Merci d\'indiquer votre code postal',
                ])
            ])
            ->add('city', TextType::class, [
                'label' => 'Votre ville :',
                'label_attr' => [
                    'class' => 'lh-label fw-medium'
                ],
                'attr' => [
                    'placeholder' => 'Merci de saisir votre ville'
                ],
                'constraints' => new NotBlank([
                    'message' => 'Merci d\'indiquer votre ville',
                ])
            ])
            ->add('phone', TextType::class, [
                'label' => 'Votre téléphone :',
                'label_attr' => [
                    'class' => 'lh-label fw-medium'
                ],
                'attr' => [
                    'placeholder' => 'Merci de saisir votre téléphone'
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => "/^(\+[0-9]{2}[.\-\s]?|00[.\-\s]?[0-9]{2}|0)([0-9]{1,3}[.\-\s]?(?:[0-9]{2}[.\-\s]?){4})$/",
                        'message' => "Le numéro de téléphone '{{ value }}' n'est pas valide."
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
