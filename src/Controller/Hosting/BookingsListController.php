<?php

namespace App\Controller\Hosting;

use App\Repository\BookingRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingsListController extends AbstractController
{
    #[Route('/hosting/reservations/all', name: 'admin_all_reservations')]
    #[IsGranted('ROLE_USER', message: 'Vous devez vous connecter pour voir vos réservations')]
    public function reservations(BookingRepository $bookingRepository): Response
    {
        $user = $this->getUser();
        $bookings = $bookingRepository->findByHost($user);

        return $this->render('admin_bookings/reservations.html.twig', [
            'withFooter' => true,
            'bookings' => $bookings
        ]);
    }

    #[Route('/hosting/reservations/completed', name: 'admin_completed_reservations')]
    #[IsGranted('ROLE_USER', message: 'Vous devez vous connecter pour voir vos réservations')]
    public function completedReservations(BookingRepository $bookingRepository): Response
    {
        $user = $this->getUser();
        $bookings = $bookingRepository->completedBookingsByHost($user);

        return $this->render('admin_bookings/completed.html.twig', [
            'withFooter' => true,
            'bookings' => $bookings
        ]);
    }

    #[Route('/hosting/reservations/upcoming', name: 'admin_upcoming_reservations')]
    #[IsGranted('ROLE_USER', message: 'Vous devez vous connecter pour voir vos réservations')]
    public function upcomingReservations(BookingRepository $bookingRepository): Response
    {
        $user = $this->getUser();
        $bookings = $bookingRepository->upcomingBookingsByHost($user);

        return $this->render('admin_bookings/upcoming.html.twig', [
            'withFooter' => true,
            'bookings' => $bookings
        ]);
    }
}