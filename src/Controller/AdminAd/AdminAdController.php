<?php

namespace App\Controller\AdminAd;

use App\Entity\Ad;
use App\Form\AdFormType\AdStep1FormType;
use App\Form\AdFormType\AdStep2FormType;
use App\Form\AdFormType\AdStep3FormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    #[Route('/become-a-host', name: 'admin_ad_intro')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    public function intro(): Response
    {
        return $this->render('admin_ad/intro.html.twig', [
            'withFooter' => true
        ]);
    }

    #[Route('/become-a-host/floor-plan', name: 'admin_ad_floor')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    public function floor(Request $request, EntityManagerInterface $em): Response
    {
        $ad = new Ad;
        $form = $this->createForm(AdStep1FormType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ad);
            $em->flush();
            return $this->redirectToRoute('admin_ad_guest', ['id' => $ad->getId()]);
        }

        return $this->render('admin_ad/floor.html.twig', [
            'form' => $form,
            'withFooter' => true
        ]);
    }

    #[Route('/become-a-host/guests/{id}', name: 'admin_ad_guest')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    public function guest(Request $request, EntityManagerInterface $em, int $id): Response
    {
        $ad = $em->getRepository(Ad::class)->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_floor');
        }
        $form = $this->createForm(AdStep2FormType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ad);
            $em->flush();

            return $this->redirectToRoute('admin_ad_location', ['id' => $ad->getId()]);
        }

        return $this->render('admin_ad/guest.html.twig', [
            'form' => $form,
            'withFooter' => true
        ]);
    }

    #[Route('/become-a-host/location/{id}', name: 'admin_ad_location')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    public function location(Request $request, EntityManagerInterface $em, int $id)
    {
        $ad = $em->getRepository(Ad::class)->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_floor');
        }

        $form = $this->createForm(AdStep3FormType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ad);
            $em->flush();
    
            return $this->redirectToRoute('admin_ad_stand', ['id' => $ad->getId()]);
        }
        return $this->render('admin_ad/location.html.twig', [
            'form' => $form,
            'withFooter' => true
        ]);
    }
}
