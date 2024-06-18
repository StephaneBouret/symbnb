<?php

namespace App\Controller\AdminAdEdit;

use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\AdEditFormType\AdStructureEditFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdEditStructureController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/hosting/editor/{id}/structure', name: 'admin_edit_structure', methods: ['GET', 'POST'])]
    public function structure(Request $request, int $id, AdRepository $adRepository): Response
    {
        $ad = $adRepository->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_listing');
        }

        $this->denyAccessUnlessGranted('CAN_EDIT', $ad, "Vous n'êtes pas le propriétaire de cette annonce");

        $form = $this->createForm(AdStructureEditFormType::class, $ad, [
            'action' => $this->generateUrl('admin_edit_structure', ['id' => $ad->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Votre modification a bien été prise en compte');
            return $this->redirectToRoute('admin_edit_structure', ['id' => $ad->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_ad_edit/structure.html.twig', [
            'withFooter' => true,
            'form' => $form,
            'ad' => $ad
        ]);
    }
    
    #[Route('/hosting/editor/{id}/structure/mobile', name: 'admin_edit_structure_mobile', methods: ['GET', 'POST'])]
    public function structureMobile(Request $request, int $id, AdRepository $adRepository): Response
    {
        $ad = $adRepository->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_listing');
        }

        $this->denyAccessUnlessGranted('CAN_EDIT', $ad, "Vous n'êtes pas le propriétaire de cette annonce");

        $form = $this->createForm(AdStructureEditFormType::class, $ad, [
            'action' => $this->generateUrl('admin_edit_structure_mobile', ['id' => $ad->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Votre modification a bien été prise en compte');
            return $this->redirectToRoute('admin_edit_structure_mobile', ['id' => $ad->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_ad_edit/structure_mobile.html.twig', [
            'withFooter' => true,
            'form' => $form,
            'ad' => $ad
        ]);
    }
}