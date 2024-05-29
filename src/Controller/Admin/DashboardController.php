<?php

namespace App\Controller\Admin;

use App\Entity\Ad;
use App\Entity\User;
use App\Entity\Author;
use App\Entity\Images;
use App\Entity\Booking;
use App\Entity\Criteria;
use App\Entity\Equipment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(AdCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Symbnb');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Annonces', 'fa-solid fa-bed', Ad::class);
        if ($this->IsGranted('ROLE_ADMIN')) {
            yield MenuItem::linkToCrud('Images', 'fas fa-image', Images::class);
            yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class);
        }
        yield MenuItem::linkToCrud('Réservations', 'fa-solid fa-umbrella-beach', Booking::class);
        if ($this->IsGranted('ROLE_ADMIN')) {
            yield MenuItem::linkToCrud('Critères-Equipement', 'fa-solid fa-mug-saucer', Criteria::class);
            yield MenuItem::linkToCrud('Equipements', 'fa-solid fa-screwdriver-wrench', Equipment::class);
        }
        yield MenuItem::linkToRoute('Retour au site', 'fas fa-home', 'homepage');
    }
}
