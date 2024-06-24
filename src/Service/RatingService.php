<?php

namespace App\Service;

use App\Entity\Booking;

class RatingService
{
    /**
     * Calcule les pourcentages des notes des commentaires
     *
     * @param Booking[] $bookings
     * @return array
     */
    public function calculateRatingPercentages(array $bookings): array
    {
        // Initialisation des compteurs pour chaque note
        $counts = [
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0,
        ];

        // Boucle à travers chaque booking
        foreach ($bookings as $booking) {
            // Récupère les commentaires associés à chaque booking
            $comments = $booking->getComments();

            foreach ($comments as $comment) {
                $rating = $comment->getRating();
                if (array_key_exists($rating, $counts)) {
                    $counts[$rating]++;
                }
            }
        }

        $totalComments = array_sum($counts);
        $percentages = [];

        if ($totalComments > 0) {
            foreach ($counts as $rating => $count) {
                $percentages[$rating] = ($count / $totalComments) * 100;
            }
        } else {
            foreach ($counts as $rating => $count) {
                $percentages[$rating] = 0;
            }
        }

        return $percentages;
    }
}