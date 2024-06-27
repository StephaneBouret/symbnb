<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\AvatarService;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'registration_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager, AvatarService $avatarService): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'action' => $this->generateUrl('registration_register')
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setFirstname(ucfirst($form->get('firstname')->getData()))
                ->setLastname(mb_strtoupper($form->get('lastname')->getData()))
                ->setCity(ucfirst($form->get('city')->getData()));
            
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $avatarService->createAndAssignAvatar($user);

            $this->addFlash('success', 'Vous êtes enregistré sur l\'application');

            return $security->login($user, LoginFormAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
