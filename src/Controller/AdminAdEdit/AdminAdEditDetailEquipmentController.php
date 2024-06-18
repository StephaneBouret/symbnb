<?php

namespace App\Controller\AdminAdEdit;

use App\Form\AdEditFormType\AdEquipmentEditFormType;
use App\Repository\AdRepository;
use App\Repository\CriteriaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdEditDetailEquipmentController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/hosting/editor/{id}/equipment/details', name: 'admin_edit_equipment_details', methods: ['GET', 'POST'])]
    public function equipmentDetails(Request $request, int $id, AdRepository $adRepository, CriteriaRepository $criteriaRepository): Response
    {
        $ad = $adRepository->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_listing');
        }

        $this->denyAccessUnlessGranted('CAN_EDIT', $ad, "Vous n'êtes pas le propriétaire de cette annonce");

        $criterias = $criteriaRepository->findAll();

        // Initialisation du formulaire avec l'entité Ad
        $form = $this->createForm(AdEquipmentEditFormType::class, $ad, [
            'criterias' => $criterias,
            'method' => 'POST',
            'action' => $this->generateUrl('admin_edit_equipment_details', ['id' => $ad->getId()]),
        ]);

        // Pré-remplissage des équipements sélectionnés
        foreach ($criterias as $criteria) {
            $selectedEquipments = $ad->getEquipment()->filter(function ($equipment) use ($criteria) {
                return $equipment->getCriteria() === $criteria;
            });

            // Prérenseigner le formulaire avec les équipements sélectionnés pour chaque critère
            $form->get('equipment_' . $criteria->getId())->setData($selectedEquipments);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($criterias as $criteria) {
                $equipmentsField = $form->get('equipment_' . $criteria->getId());
                $selectedEquipments = $equipmentsField->getData();

                // Ajouter ou supprimer des équipements de l'annonce selon la sélection
                foreach ($criteria->getEquipment() as $equipment) {
                    if ($selectedEquipments->contains($equipment)) {
                        $ad->addEquipment($equipment);
                    } else {
                        $ad->removeEquipment($equipment);
                    }
                }
            }

            $this->em->flush();
            $this->addFlash('success', 'Votre modification a bien été prise en compte');

            // Redirection en fonction de la taille de l'écran
            $isMobile = $request->request->get('is_mobile', '0') === '1';
            $redirectRoute = $isMobile ? 'admin_edit_equipment_mobile' : 'admin_edit_equipment';
            return $this->redirectToRoute($redirectRoute, ['id' => $ad->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_ad_edit/equipment_details.html.twig', [
            'withFooter' => true,
            'ad' => $ad,
            'criterias' => $criterias,
            'form' => $form,
        ]);
    }
}
