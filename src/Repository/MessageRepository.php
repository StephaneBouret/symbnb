<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * Récupère les messages reçus par l'hôte, ventilés par annonce et par utilisateur.
     *
     * @param integer $id
     * @return array
     */
    public function findMessagesReceivedByHostGroupedByAdAndUser(int $id): array
    {
        $qb = $this->createQueryBuilder('m')
            ->select('ad.id AS adId, guest.id AS guestId, m')
            ->innerJoin('m.ad', 'ad')
            ->innerJoin('m.guest', 'guest')
            ->where('m.host = :hostId')
            ->setParameter('hostId', $id)
            ->orderBy('m.createdAt', 'DESC');

        $result = $qb->getQuery()->getResult();

        $messagesByAdAndUser = [];
        foreach ($result as $row) {
            $adId = $row['adId'];
            $guestId = $row['guestId'];
            $message = $row[0];
            // Si le message pour cette combinaison n'est pas encore enregistré, l'ajouter
            if (!isset($messagesByAdAndUser[$adId][$guestId])) {
                $messagesByAdAndUser[$adId][$guestId] = $message;
            }
        }

        return $messagesByAdAndUser;
    }

    /**
     * Récupère tous les messages échangés entre l'hôte et un invité spécifique pour une annonce donnée.
     *
     * @param integer $adId
     * @param integer $hostId
     * @param integer $guestId
     * @return array
     */
    public function findMessagesBetweenHostAndGuestForAd(int $adId, int $hostId, int $guestId): array
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.ad = :adId')
            ->andWhere('(m.host = :hostId AND m.guest = :guestId) OR (m.host = :guestId AND m.guest = :hostId)')
            ->setParameter('adId', $adId)
            ->setParameter('hostId', $hostId)
            ->setParameter('guestId', $guestId)
            ->orderBy('m.createdAt', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Récupère les messages reçus par le guest, ventilés par annonce et par hôte.
     *
     * @param integer $id
     * @param array $excludedAdIds
     * @return void
     */
    public function findMessagesReceivedByGuestGroupedByAdAndHost(int $id, array $excludedAdIds)
    {
        $qb = $this->createQueryBuilder('m')
            ->select('ad.id AS adId, host.id AS hostId, guest.id AS guestId, m')
            ->innerJoin('m.ad', 'ad')
            ->innerJoin('m.host', 'host')
            ->innerJoin('m.guest', 'guest')
            ->where('m.host = :guestId')
            ->setParameter('guestId', $id);

        if (!empty($excludedAdIds)) {
            $qb->andWhere($qb->expr()->notIn('m.ad', ':excludedAdIds'))
                ->setParameter('excludedAdIds', $excludedAdIds);
        }

        $qb->orderBy('m.createdAt', 'DESC');

        $result = $qb->getQuery()->getResult();
        
        $messagesByAdAndUser = [];
        foreach ($result as $row) {
            $adId = $row['adId'];
            $guestId = $row['guestId'];
            $message = $row[0];
            if (!isset($messagesByAdAndUser[$adId][$guestId])) {
                $messagesByAdAndUser[$adId][$guestId] = $message;
            }
        }

        return $messagesByAdAndUser;
    }

    /**
     * Récupère tous les messages échangés entre l'invité et un hôte spécifique pour une annonce donnée.
     *
     * @param integer $adId
     * @param integer $hostId
     * @param integer $guestId
     * @return array
     */
    public function findMessagesBetweenGuestAndHostForAd(int $adId, int $hostId, int $guestId): array
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.ad = :adId')
            ->andWhere('(m.guest = :guestId AND m.host = :hostId) OR (m.guest = :hostId AND m.host = :guestId)')
            ->setParameter('adId', $adId)
            ->setParameter('hostId', $hostId)
            ->setParameter('guestId', $guestId)
            ->orderBy('m.createdAt', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
