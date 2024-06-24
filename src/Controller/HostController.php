<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class HostController extends AbstractController
{
    #[Route('/host/show/{id}', name: 'host_show', priority: -1)]
    public function show($id, UserRepository $userRepository)
    {
        $host = $userRepository->find($id);
        
        if (!$host) {
            throw $this->createNotFoundException("L'hôte demandé n'existe pas");
        }

        $ads = $host->getAds();
        if ($ads->isEmpty()) {
            throw $this->createNotFoundException("Cet utilisateur n'a créé aucune annonce.");
        }

        $avgRating = $host->getAvgRatingForHost();
        $allComments = $host->getAllComments();
        $totalComments = count($allComments);

        return $this->render('host/show.html.twig', [
            'host' => $host,
            'totalComments' => $totalComments,
            'avgRating' => $avgRating,
            'allComments' => $allComments
        ]);
    }
}