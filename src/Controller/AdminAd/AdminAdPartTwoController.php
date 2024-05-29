<?php

namespace App\Controller\AdminAd;

use App\Entity\Ad;
use App\Repository\CriteriaRepository;
use App\Form\AdFormType\AdStep4FormType;
use App\Form\AdFormType\AdStep5FormType;
use App\Form\AdFormType\AdStep6FormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdPartTwoController extends AbstractController
{
    #[Route('/become-a-host/stand-out/{id}', name: 'admin_ad_stand')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    public function stand(EntityManagerInterface $em, int $id): Response
    {
        $ad = $em->getRepository(Ad::class)->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_floor');
        }

        return $this->render('admin_ad/stand.html.twig', [
            'withFooter' => true,
            'id' => $ad->getId()
        ]);
    }

    #[Route('/become-a-host/title/{id}', name: 'admin_ad_title')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    public function title(Request $request, EntityManagerInterface $em, int $id): Response
    {
        $ad = $em->getRepository(Ad::class)->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_floor');
        }

        $form = $this->createForm(AdStep4FormType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ad);
            $em->flush();

            return $this->redirectToRoute('admin_ad_description', ['id' => $ad->getId()]);
        }

        return $this->render('admin_ad/title.html.twig', [
            'withFooter' => true,
            'form' => $form
        ]);
    }

    #[Route('/become-a-host/description/{id}', name: 'admin_ad_description')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    public function description(Request $request, EntityManagerInterface $em, int $id): Response
    {
        $ad = $em->getRepository(Ad::class)->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_floor');
        }

        $form = $this->createForm(AdStep5FormType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ad);
            $em->flush();

            return $this->redirectToRoute('admin_ad_equipment', ['id' => $ad->getId()]);
        }

        return $this->render('admin_ad/description.html.twig', [
            'withFooter' => true,
            'form' => $form
        ]);
    }

    #[Route('/become-a-host/equipments/{id}', name: 'admin_ad_equipment')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    public function equipment(Request $request, EntityManagerInterface $em, int $id, CriteriaRepository $criteriaRepository): Response
    {
        $criterias = $criteriaRepository->findAll();
        $ad = $em->getRepository(Ad::class)->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_floor');
        }

        $form = $this->createForm(AdStep6FormType::class, $ad, [
            'criterias' => $criterias,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Effacer les équipments courants
            foreach ($ad->getEquipment() as $equipment) {
                $ad->removeEquipment($equipment);
            }

            // Ajoute les équipements sélectionnés
            foreach ($criterias as $criteria) {
                $selectedEquipment = $form->get('equipment_' . $criteria->getId())->getData();
                foreach ($selectedEquipment as $equipment) {
                    $ad->addEquipment($equipment);
                }
            }
            $em->persist($ad);
            $em->flush();

            return $this->redirectToRoute('admin_ad_setup', ['id' => $ad->getId()]);
        }

        return $this->render('admin_ad/equipment.html.twig', [
            'withFooter' => true,
            'form' => $form,
            'criterias' => $criterias,
        ]);
    }
}
