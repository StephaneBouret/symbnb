<?php

namespace App\Controller\Message;

use App\Entity\User;
use App\Entity\Message;
use App\Form\MessageFormType;
use App\Repository\AdRepository;
use App\Repository\BookingRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessageHostController extends AbstractController
{
    public function __construct(protected EntityManagerInterface $em)
    {
    }

    #[Route('/hosting/messages/send/{id}', name: 'host_send_message')]
    #[IsGranted('ROLE_USER', message: 'Vous devez vous connecter pour envoyer un message')]
    public function sendHostMessage($id, Request $request, BookingRepository $bookingRepository): Response
    {
        $booking = $bookingRepository->find($id);
        if (!$booking) {
            throw $this->createNotFoundException("La réservation demandée n'existe pas");
        }

        // Vérifier que l'utilisateur connecté est bien le propriétaire de l'annonce liée à la réservation
        $ad = $booking->getAd();
        if ($ad->getAuthor() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à envoyer un message pour cette réservation");
        }

        $referer = $request->headers->get('referer');

        // L'utilisateur ayant fait la réservation (invité)
        $guest = $booking->getBooker();

        $message = new Message;
        $form = $this->createForm(MessageFormType::class, $message, [
            'action' => $this->generateUrl('message_send', ['id' => $booking->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setAd($ad)
                ->setHost($this->getUser())
                ->setGuest($guest);

            $this->em->persist($message);
            $this->em->flush();

            $this->addFlash('success', 'Votre message a bien été envoyé');

            return $this->redirectToRoute('ads_show', ['slug' => $ad->getSlug()]);
        }

        return $this->render('message/send_host_message.html.twig', [
            'withFooter' => true,
            'booking' => $booking,
            'referer' => $referer,
            'form' => $form,
        ]);
    }

    #[Route('/hosting/messages/{id}', name: 'host_view_messages')]
    #[IsGranted('ROLE_USER', message: 'Vous devez vous connecter pour voir les messages')]
    public function viewHostMessages($id, AdRepository $adRepository, MessageRepository $messageRepository, Request $request, UserRepository $userRepository): Response
    {
        /** @var User $host */
        $host = $this->getUser();

        // Vérifie que l'utilisateur connecté est bien le propriétaire de l'ID fourni
        if ($host->getId() !== (int)$id) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à voir ces messages");
        }

        // Récupérer toutes les annonces de cet hôte
        $ads = $adRepository->findBy(['author' => $host]);

        $messagesByAdAndUser = $messageRepository->findMessagesReceivedByHostGroupedByAdAndUser($host->getId());

        // Vérifier s'il y a une annonce et un utilisateur spécifiques sélectionnés
        $selectedAdId = $request->query->get('adId');
        $selectedUserId = $request->query->get('userId');
        $selectedAdMessages = [];
        if ($selectedAdId && $selectedUserId) {
            $selectedAdMessages = $messageRepository->findMessagesBetweenHostAndGuestForAd($selectedAdId, $host->getId(), $selectedUserId);
        }

        $message = new Message;
        $form = $this->createForm(MessageFormType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setAd($adRepository->find($selectedAdId))
                    ->setHost($userRepository->find($selectedUserId))
                    ->setGuest($host);

            $this->em->persist($message);
            $this->em->flush();

            $this->addFlash('success', 'Votre message a bien été envoyé');

            return $this->redirectToRoute('host_view_messages', [
                'id' => $host->getId(),
                'adId' => $selectedAdId,
                'userId' => $selectedUserId
            ]);
        }

        return $this->render('message/host_messages.html.twig', [
            'withFooter' => true,
            'ads' => $ads,
            'messagesByAdAndUser' => $messagesByAdAndUser,
            'selectedAdId' => $selectedAdId,
            'selectedUserId' => $selectedUserId,
            'selectedAdMessages' => $selectedAdMessages,
            'host' => $host,
            'hostId' => $host->getId(),
            'form' => $form
        ]);
    }
}
