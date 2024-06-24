<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Repository\BookingRepository;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
    #[Route('/comments/reviews', name: 'comments_reviews')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à vos commentaires')]
    public function reviews(CommentRepository $commentRepository, BookingRepository $bookingRepository): Response
    {
        /** @var User */
        $user = $this->getUser();
        $comments = $commentRepository->findBy([
            'author' => $user->getId()
        ]);

        $today = new DateTime();
        $bookings = $bookingRepository->findBy([
            'booker' => $user->getId()
        ]);

        $hasPendingComments = false;

        foreach ($bookings as $booking) {
            if ($booking->getEndDateAt() < $today) {
                $comment = $booking->getCommentFromAuthor($user);
                if (!$comment) {
                    $hasPendingComments = true;
                    break;
                }    
            }
        }

        return $this->render('comment/reviews.html.twig', [
            'comments' => $comments,
            'hasPendingComments' => $hasPendingComments
        ]);
    }
}
