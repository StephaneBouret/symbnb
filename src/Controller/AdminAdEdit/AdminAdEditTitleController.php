<?php

namespace App\Controller\AdminAdEdit;

use App\Repository\AdRepository;
use App\Form\AdFormType\AdStep4FormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdEditTitleController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/hosting/editor/{id}/title', name: 'admin_edit_title', methods: ['GET', 'POST'])]
    public function title(Request $request, int $id, AdRepository $adRepository): Response
    {
        $ad = $adRepository->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_listing');
        }

        $this->denyAccessUnlessGranted('CAN_EDIT', $ad, "Vous n'êtes pas le propriétaire de cette annonce");

        $form = $this->createForm(AdStep4FormType::class, $ad, [
            'action' => $this->generateUrl('admin_edit_title', ['id' => $ad->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingAd = $adRepository->findOneBy(['name' => $ad->getName()]);
            if ($existingAd) {
                $this->addFlash('warning', 'Le titre existe déjà. Veuillez choisir un autre titre.');
                return $this->redirectToRoute('admin_edit_title', ['id' => $ad->getId()]);
            }
            $this->em->flush();
            $this->addFlash('success', 'Votre modification a bien été prise en compte');
            return $this->redirectToRoute('admin_edit_title', ['id' => $ad->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_ad_edit/title.html.twig', [
            'withFooter' => true,
            'form' => $form,
            'ad' => $ad
        ]);
    }

    #[Route('/hosting/editor/{id}/title/mobile', name: 'admin_edit_title_mobile', methods: ['GET', 'POST'])]
    public function titleMobile(Request $request, int $id, AdRepository $adRepository): Response
    {
        $ad = $adRepository->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_listing');
        }

        $this->denyAccessUnlessGranted('CAN_EDIT', $ad, "Vous n'êtes pas le propriétaire de cette annonce");

        $form = $this->createForm(AdStep4FormType::class, $ad, [
            'action' => $this->generateUrl('admin_edit_title_mobile', ['id' => $ad->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingAd = $adRepository->findOneBy(['name' => $ad->getName()]);
            if ($existingAd) {
                $this->addFlash('warning', 'Le titre existe déjà. Veuillez choisir un autre titre.');
                return $this->redirectToRoute('admin_edit_title_mobile', ['id' => $ad->getId()]);
            }
            $this->em->flush();
            $this->addFlash('success', 'Votre modification a bien été prise en compte');
            return $this->redirectToRoute('admin_edit_title_mobile', ['id' => $ad->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_ad_edit/title_mobile.html.twig', [
            'withFooter' => true,
            'form' => $form,
            'ad' => $ad
        ]);
    }
}
