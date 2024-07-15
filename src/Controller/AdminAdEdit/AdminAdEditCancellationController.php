<?php

namespace App\Controller\AdminAdEdit;

use App\Form\AdEditFormType\AdCancellationEditFormType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdEditCancellationController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/hosting/editor/{id}/cancellation-policy', name: 'admin_edit_cancellation')]
    public function cancellation(int $id, AdRepository $adRepository): Response
    {
        $ad = $adRepository->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_listing');
        }

        $this->denyAccessUnlessGranted('CAN_EDIT', $ad, "Vous n'êtes pas le propriétaire de cette annonce");

        return $this->render('admin_ad_edit/cancellation.html.twig', [
            'withFooter' => true,
            'ad' => $ad
        ]);
    }

    #[Route('/hosting/editor/{id}/cancellation-policy/mobile', name: 'admin_edit_cancellation_mobile', methods: ['GET', 'POST'])]
    public function equipmentMobile(int $id, AdRepository $adRepository): Response
    {
        $ad = $adRepository->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_listing');
        }

        $this->denyAccessUnlessGranted('CAN_EDIT', $ad, "Vous n'êtes pas le propriétaire de cette annonce");

        return $this->render('admin_ad_edit/cancellation_mobile.html.twig', [
            'withFooter' => true,
            'ad' => $ad
        ]);
    }

    #[Route('/hosting/editor/{id}/cancellation-policy/standard', name: 'admin_edit_cancellation_standard', methods: ['GET', 'POST'])]
    public function cancellationStandard(Request $request, int $id, AdRepository $adRepository): Response
    {
        $ad = $adRepository->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_listing');
        }

        $this->denyAccessUnlessGranted('CAN_EDIT', $ad, "Vous n'êtes pas le propriétaire de cette annonce");

        $form = $this->createForm(AdCancellationEditFormType::class, $ad, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_edit_cancellation_standard', ['id' => $ad->getId()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cancellationId = $form->get('cancellation')->getData();
            $ad->setCancellation($cancellationId);

            $this->em->flush();
            $this->addFlash('success', 'Votre modification a bien été prise en compte');
            return $this->redirectToRoute('admin_edit_cancellation', ['id' => $ad->getId()]);
        }

        return $this->render('admin_ad_edit/cancellation_standard.html.twig', [
            'withFooter' => true,
            'ad' => $ad,
            'form' => $form,
        ]);
    }
}
