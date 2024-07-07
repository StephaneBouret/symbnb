<?php

namespace App\Controller\Message;

use App\Entity\User;
use App\Entity\Message;
use App\Form\MessageFormType;
use App\Repository\AdRepository;
use App\Repository\UserRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessageGuestController extends AbstractController
{
    public function __construct(protected EntityManagerInterface $em)
    {
    }

    #[Route('contact_host/{id}/send_message', name: 'message_send')]
    #[IsGranted('ROLE_USER', message: 'Vous devez vous connecter pour envoyer un message')]
    public function sendMessage($id, AdRepository $adRepository, Request $request): Response
    {
        $ad = $adRepository->find($id);
        $host = $ad->getAuthor();

        if (!$host) {
            throw $this->createNotFoundException("Cet utilisateur n'a créé aucune annonce.");
        }

        $message = new Message;
        $form = $this->createForm(MessageFormType::class, $message, [
            'action' => $this->generateUrl('message_send', ['id' => $ad->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setAd($ad)
                ->setGuest($this->getUser())
                ->setHost($host);

            $this->em->persist($message);
            $this->em->flush();

            $this->addFlash('success', 'Votre message a bien été envoyé');

            return $this->redirectToRoute('ads_show', ['slug' => $ad->getSlug()]);
        }

        return $this->render('message/send_message.html.twig', [
            'withFooter' => true,
            'host' => $host,
            'ad' => $ad,
            'form' => $form,
        ]);
    }

    #[Route('/guest/messages/{id}', name: 'guest_view_messages')]
    #[IsGranted('ROLE_USER', message: 'Vous devez vous connecter pour voir les messages')]
    public function viewGuestMessages($id, AdRepository $adRepository, MessageRepository $messageRepository, Request $request, UserRepository $userRepository): Response
    {
        /** @var User $guest */
        $guest = $this->getUser();

        if ($guest->getId() !== (int)$id) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à voir ces messages");
        }

        $adIds = $adRepository->findAdIdsByUser($guest->getId());

        $messagesByAdAndUser = $messageRepository->findMessagesReceivedByGuestGroupedByAdAndHost($guest->getId(), $adIds);

        $selectedAdId = $request->query->get('adId');
        $selectedUserId = $request->query->get('userId');
        $selectedAdMessages = [];
        if ($selectedAdId && $selectedUserId) {
            $selectedAdMessages = $messageRepository->findMessagesBetweenGuestAndHostForAd($selectedAdId, $guest->getId(), $selectedUserId);
        }

        $message = new Message;
        $form = $this->createForm(MessageFormType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setAd($adRepository->find($selectedAdId))
                    ->setHost($userRepository->find($selectedUserId))
                    ->setGuest($guest);

            $this->em->persist($message);
            $this->em->flush();

            $this->addFlash('success', 'Votre message a bien été envoyé');

            return $this->redirectToRoute('guest_view_messages', [
                'id' => $guest->getId(),
                'adId' => $selectedAdId,
                'userId' => $selectedUserId
            ]);
        }

        return $this->render('message/guest_messages.html.twig', [
            'withFooter' => true,
            'messagesByAdAndUser' => $messagesByAdAndUser,
            'selectedAdId' => $selectedAdId,
            'selectedUserId' => $selectedUserId,
            'selectedAdMessages' => $selectedAdMessages,
            'guest' => $guest,
            'guestId' => $guest->getId(),
            'form' => $form
        ]);
    }
}
