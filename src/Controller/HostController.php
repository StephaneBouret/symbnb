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

        return $this->render('host/show.html.twig', [
            'host' => $host,
        ]);
    }
}