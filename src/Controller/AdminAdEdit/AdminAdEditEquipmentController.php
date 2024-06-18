<?php

namespace App\Controller\AdminAdEdit;

use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdEditEquipmentController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/hosting/editor/{id}/equipment', name: 'admin_edit_equipment', methods: ['GET', 'POST'])]
    public function equipment(Request $request, int $id, AdRepository $adRepository): Response
    {
        $ad = $adRepository->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_listing');
        }

        $this->denyAccessUnlessGranted('CAN_EDIT', $ad, "Vous n'êtes pas le propriétaire de cette annonce");

        if ($request->isMethod('POST')) {
            $equipmentIds = $request->request->all('equipments');

            foreach ($equipmentIds as $equipmentId) {
                $equipment = $ad->getEquipment()->filter(function ($eq) use ($equipmentId) {
                    return $eq->getId() == $equipmentId;
                })->first();

                if ($equipment) {
                    $ad->removeEquipment($equipment);
                }
            }

            $this->em->flush();
            $this->addFlash('success', 'Votre suppression a bien été prise en compte');
            return $this->redirectToRoute('admin_edit_equipment', ['id' => $ad->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_ad_edit/equipment.html.twig', [
            'withFooter' => true,
            'ad' => $ad
        ]);
    }

    #[Route('/hosting/editor/{id}/equipment/mobile', name: 'admin_edit_equipment_mobile', methods: ['GET', 'POST'])]
    public function equipmentMobile(Request $request, int $id, AdRepository $adRepository): Response
    {
        $ad = $adRepository->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_listing');
        }

        $this->denyAccessUnlessGranted('CAN_EDIT', $ad, "Vous n'êtes pas le propriétaire de cette annonce");

        if ($request->isMethod('POST')) {
            $equipmentIds = $request->request->all('equipments');

            foreach ($equipmentIds as $equipmentId) {
                $equipment = $ad->getEquipment()->filter(function ($eq) use ($equipmentId) {
                    return $eq->getId() == $equipmentId;
                })->first();

                if ($equipment) {
                    $ad->removeEquipment($equipment);
                }
            }

            $this->em->flush();
            $this->addFlash('success', 'Votre suppression a bien été prise en compte');
            return $this->redirectToRoute('admin_edit_equipment_mobile', ['id' => $ad->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_ad_edit/equipment_mobile.html.twig', [
            'withFooter' => true,
            'ad' => $ad
        ]);
    }
}
