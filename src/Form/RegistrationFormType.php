<?php

namespace App\Form;

use App\Entity\User;
use libphonenumber\PhoneNumberFormat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre email :',
                'label_attr' => [
                    'class' => 'lh-label fw-medium'
                ],
                'required' => true,
                'attr' => [
                    'placeholder' => 'Merci de saisir votre adresse email'
                ],
                'constraints' => new Email()
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions d\utilisation',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'required' => true,
                'invalid_message' => 'Le mot de passe et la confirmation doivent être identiques',
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => 'Merci de saisir votre mot de passe'
                ],
                'constraints' => [
                    new PasswordStrength([
                        'minScore' => PasswordStrength::STRENGTH_STRONG,
                        'message' => 'Le mot de passe est trop faible. Veuillez utiliser un mot de passe plus fort.'
                    ])
                ],
                'first_options' => [
                    'label' => 'Votre mot de passe :',
                    'label_attr' => [
                        'class' => 'lh-label fw-medium'
                    ],
                    'attr' => [
                        'placeholder' => 'Merci de saisir votre mot de passe'
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmez votre mot de passe :',
                    'label_attr' => [
                        'class' => 'lh-label fw-medium'
                    ],
                    'attr' => [
                        'placeholder' => 'Merci de confirmer votre mot de passe'
                    ]
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Votre prénom :',
                'label_attr' => [
                    'class' => 'lh-label fw-medium'
                ],
                'attr' => [
                    'placeholder' => 'Merci de saisir votre prénom'
                ],
                'constraints' => new Length([
                    'min' => 2,
                    'max' => 30,
                    'minMessage' => 'Votre prénom doit comporter au moins {{ limit }} caractères',
                    'maxMessage' => 'Votre prénom ne peut excéder {{ limit }} caractères',
                ])
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Votre nom :',
                'label_attr' => [
                    'class' => 'lh-label fw-medium'
                ],
                'attr' => [
                    'placeholder' => 'Merci de saisir votre nom'
                ],
                'constraints' => new NotBlank([
                    'message' => 'Merci d\'indiquer votre nom',
                ])
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
            ->add('phone', PhoneNumberType::class, [
                'default_region' => 'FR',
                'format' => PhoneNumberFormat::NATIONAL,
                'label' => 'Votre téléphone :',
                'label_attr' => [
                    'class' => 'lh-label fw-medium'
                ],
                'attr' => [
                    'placeholder' => 'Merci de saisir votre téléphone'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
