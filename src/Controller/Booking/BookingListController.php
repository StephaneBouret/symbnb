<?php

namespace App\Controller\Booking;

use App\Entity\Comment;
use App\Entity\User;
use App\Form\CommentFormType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingListController extends AbstractController
{
    #[Route('/bookings', name: 'booking_index')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à vos voyages')]
    public function index(BookingRepository $bookingRepository): Response
    {
        // 1. Nous devons nous assurer que la personne est connectée sinon redirection vers la page d'accueil
        // 2. Nous voulons savoir qui est connecté ?
        /** @var User */
        $user = $this->getUser();

        $futureBookings = $bookingRepository->findFutureBookings($user);
        $pastBookings = $bookingRepository->findPastBookings($user);

        // 3. Nous voulons passer l'utilisateur à twig afin d'afficher ses commandes
        return $this->render('booking/index.html.twig', [
            'futureBookings' => $futureBookings,
            'pastBookings' => $pastBookings
        ]);
    }

    #[Route('/booking/show/{id}', name: 'booking_show', requirements: ["id" => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function show(Request $request, $id, BookingRepository $bookingRepository, EntityManagerInterface $em): Response
    {
        $booking = $bookingRepository->findOneBy([
            'id' => $id
        ]);

        if (!$booking) {
            throw $this->createNotFoundException("La réservation demandée n'existe pas");
        }

        $comment = new Comment;
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setBooking($booking)
                ->setAuthor($this->getUser());

            $em->persist($comment);
            $em->flush();
            $this->addFlash('success', 'Votre commentaire a bien été pris en compte');
            return $this->redirectToRoute('booking_show', ['id' => $booking->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
            'form' => $form
        ]);
    }
}
