<?php

namespace App\Controller\AdminAdEdit;

use App\Repository\AdRepository;
use App\Form\AdFormType\AdStep8FormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdEditPicturesController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/hosting/editor/{id}/pictures', name: 'admin_edit_pictures', methods: ['GET', 'POST'])]
    public function pictures(Request $request, int $id, AdRepository $adRepository): Response
    {
        $ad = $adRepository->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_listing');
        }

        $this->denyAccessUnlessGranted('CAN_EDIT', $ad, "Vous n'êtes pas le propriétaire de cette annonce");

        $form = $this->createForm(AdStep8FormType::class, $ad, [
            'action' => $this->generateUrl('admin_edit_pictures', ['id' => $ad->getId()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();

            foreach ($images as $image) {
                if ($image->getUpdatedAt() === null) {
                    $ad->removeImage($image);
                    $this->em->remove($image);
                }
            }
            $this->em->flush();
            $this->addFlash('success', 'Votre modification a bien été prise en compte');
            return $this->redirectToRoute('admin_edit_pictures', ['id' => $ad->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_ad_edit/pictures.html.twig', [
            'withFooter' => true,
            'form' => $form,
            'ad' => $ad,
        ]);
    }

    #[Route('/hosting/editor/{id}/pictures/mobile', name: 'admin_edit_pictures_mobile', methods: ['GET', 'POST'])]
    public function picturesMobile(Request $request, int $id, AdRepository $adRepository): Response
    {
        $ad = $adRepository->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_listing');
        }

        $this->denyAccessUnlessGranted('CAN_EDIT', $ad, "Vous n'êtes pas le propriétaire de cette annonce");

        $form = $this->createForm(AdStep8FormType::class, $ad, [
            'action' => $this->generateUrl('admin_edit_pictures_mobile', ['id' => $ad->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();

            foreach ($images as $image) {
                if ($image->getUpdatedAt() === null) {
                    $ad->removeImage($image);
                    $this->em->remove($image);
                }
            }
            $this->em->flush();
            $this->addFlash('success', 'Votre modification a bien été prise en compte');
            return $this->redirectToRoute('admin_edit_pictures_mobile', ['id' => $ad->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_ad_edit/pictures_mobile.html.twig', [
            'withFooter' => true,
            'form' => $form,
            'ad' => $ad
        ]);
    }
}
