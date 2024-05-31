<?php

namespace App\Controller\AdminAd;

use App\Entity\Ad;
use App\Form\AdFormType\AdStep7FormType;
use App\Form\AdFormType\AdStep8FormType;
use App\Form\AdFormType\AdStep9FormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdPartThreeController extends AbstractController
{
    #[Route('/become-a-host/finish-setup/{id}', name: 'admin_ad_setup')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    public function setup(EntityManagerInterface $em, int $id): Response
    {
        $ad = $em->getRepository(Ad::class)->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_floor');
        }

        return $this->render('admin_ad/finish-setup.html.twig', [
            'withFooter' => true,
            'id' => $ad->getId()
        ]);
    }

    #[Route('/become-a-host/price/{id}', name: 'admin_ad_price')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    public function price(Request $request, EntityManagerInterface $em, int $id): Response
    {
        $ad = $em->getRepository(Ad::class)->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_floor');
        }

        $form = $this->createForm(AdStep7FormType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ad);
            $em->flush();

            return $this->redirectToRoute('admin_ad_pictures', ['id' => $ad->getId()]);
        }

        return $this->render('admin_ad/price.html.twig', [
            'withFooter' => true,
            'form' => $form
        ]);
    }

    #[Route('/become-a-host/pictures/{id}', name: 'admin_ad_pictures')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    public function pictures(Request $request, EntityManagerInterface $em, int $id): Response
    {
        $ad = $em->getRepository(Ad::class)->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_floor');
        }

        $form = $this->createForm(AdStep8FormType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ad);
            $em->flush();

            return $this->redirectToRoute('admin_ad_save', ['id' => $ad->getId()]);
        }

        return $this->render('admin_ad/pictures.html.twig', [
            'withFooter' => true,
            'form' => $form
        ]);
    }

    #[Route('/become-a-host/save/{id}', name: 'admin_ad_save')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    public function saveAd(Request $request, EntityManagerInterface $em, int $id): Response
    {
        $ad = $em->getRepository(Ad::class)->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_floor');
        }

        $user = $this->getUser();

        $form = $this->createForm(AdStep9FormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();

            $ad->setAuthor($user);
            $em->persist($ad);
            $em->flush();

            $this->addFlash('success', 'Votre annonce a bien été enregistrée');
            return $this->redirectToRoute('ads_show', ['slug' => $ad->getSlug()]);
        }

        return $this->render('admin_ad/save.html.twig', [
            'form' => $form
        ]);
    }
}
