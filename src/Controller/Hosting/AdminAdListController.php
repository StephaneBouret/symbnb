<?php

namespace App\Controller\Hosting;

use App\Entity\Ad;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdListController extends AbstractController
{
    #[Route('/hosting/listing', name: 'admin_ad_listing')]
    #[IsGranted('ROLE_USER', message: 'Vous devez vous connecter pour voir vos annonces')]
    public function listing(AdRepository $adRepository): Response
    {
        $user = $this->getUser();
        $ads = $adRepository->findBy(
            ['author' => $user],
            ['createdAt' => 'DESC']
        );

        if (!$ads) {
            throw $this->createNotFoundException("Vous n'avez pas d'annonces !");
        }

        return $this->render('admin_ad/index.html.twig', [
            'withFooter' => true,
            'ads' => $ads
        ]);
    }

    #[Route('hosting/ad/{id}/delete', name: 'ad_delete')]
    public function delete(Request $request, Ad $ad, AdRepository $adRepository, EntityManagerInterface $em): Response
    {
        // Vérification des réservations
        if ($ad->getBookings()->count() > 0) {
            $this->addFlash('warning', 'Impossible de supprimer une annonce ayant des réservations.');
            return $this->redirectToRoute('admin_ad_listing', [], Response::HTTP_SEE_OTHER);
        }

        // Vérification du token CSRF
        if ($this->isCsrfTokenValid('delete' . $ad->getId(), $request->request->get('_token'))) {
            // Suppression des images associées avant de supprimer l'annonce
            foreach ($ad->getImages() as $image) {
                $em->remove($image);
            }
            $em->flush();

            // Suppression de l'annonce
            $adRepository->remove($ad, true);

            $this->addFlash('success', 'L\'annonce a bien été supprimée !');
        }

        return $this->redirectToRoute('admin_ad_listing', [], Response::HTTP_SEE_OTHER);
    }
}