<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Service\AvatarService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class EasyAdminAvatarSubscriber implements EventSubscriberInterface
{
    public function __construct(protected AvatarService $avatarService, protected ParameterBagInterface $params)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['createAvatar', 0],
            BeforeEntityUpdatedEvent::class => ['updateAvatar', 0],
        ];
    }

    public function createAvatar(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof User) {
            return;
        }

        // Si l'utilisateur n'a pas d'avatar, créer un avatar par défaut
        if ($entity->getAvatar() === null) {
            $this->avatarService->createAndAssignAvatar($entity);
        }
    }

    public function updateAvatar(BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof User) {
            return;
        }

        // Si l'avatar est modifié dans le formulaire, mettre à jour l'avatar
        $avatar = $entity->getAvatar();

        if ($avatar !== null && method_exists($avatar, 'getImageName') && $avatar->getImageName() === null) {
            $initial = strtoupper(substr($entity->getFirstname(), 0, 1));
            $avatarsDirectory = $this->params->get('avatars_directory');
            $outputPath = $avatarsDirectory . '/' . uniqid() . '.png';
            $avatar->createDefaultAvatar($initial, $outputPath);
            $avatar->setImageName(basename($outputPath));
        }
    }
}