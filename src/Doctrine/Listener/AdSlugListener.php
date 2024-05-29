<?php

namespace App\Doctrine\Listener;

use App\Entity\Ad;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Ad::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Ad::class)]
class AdSlugListener
{
    protected $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(Ad $entity, PrePersistEventArgs $event)
    {
        $this->setSlug($entity);
    }

    public function preUpdate(Ad $entity, PreUpdateEventArgs $event)
    {
        $this->setSlug($entity);
    }

    private function setSlug(Ad $entity)
    {
        // Check if the name is set and not null
        if ($entity->getName() !== null && empty($entity->getSlug())) {
            $entity->setSlug(strtolower($this->slugger->slug($entity->getName())));
        }
    }
}