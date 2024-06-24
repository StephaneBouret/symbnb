<?php

namespace App\Entity;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BookingRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Booking
{
    public const STATUS_PENDING = 'PENDING';
    public const STATUS_PAID = 'PAID';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $booker = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ad $ad = null;

    #[ORM\Column]
    #[Assert\GreaterThanOrEqual('today', message: 'La date d\'arrivée doit être supérieure ou égale à la date d\'aujourd\'hui !')]
    private ?\DateTimeImmutable $startDateAt = null;

    #[ORM\Column]
    #[Assert\GreaterThan(propertyPath: "startDateAt", message: "La date de départ doit être plus éloignée que la date d'arrivée !")]
    private ?\DateTimeImmutable $endDateAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?int $amount = null;

    #[ORM\Column(length: 255)]
    private ?string $status = 'PENDING';

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'booking', orphanRemoval: true)]
    private Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function prePersist()
    {
        if (empty($this->createdAt)) {
            $this->createdAt = new DateTimeImmutable();
        }

        if (empty($this->amount)) {
            // Prix de l'annonce * nombre de jour
            $this->amount = $this->ad->getPrice() * $this->getDuration();
        }
    }

    /**
     * Permet de récupérer le commentaire d'un user-auteur par rapport à une annonce
     *
     * @param User $author
     * @return Comment|null
     */
    public function getCommentFromAuthor(User $author)
    {
        foreach ($this->comments as $comment) {
            if ($comment->getAuthor() === $author) return $comment;
        }

        return null;
    }

    /**
     * Permet de savoir si les dates réservées sont disponibles ou non
     *
     * @return boolean
     */
    public function isBookableDates()
    {
        // 1. Il faut connaitre les dates qui sont impossibles pour l'annonce
        $notAvailableDays = $this->ad->getNotAvailableDays();
        // 2. Il faut comparer les dates choisies avec les dates impossibles
        $bookingDays = $this->getDays();

        $formatDay = function ($day) {
            return $day->format('Y-m-d');
        };

        // Tableau des chaines de caractères de mes journées
        $days = array_map($formatDay, $bookingDays);
        $notAvailable = array_map($formatDay, $notAvailableDays);

        foreach ($days as $day) {
            if (array_search($day, $notAvailable) !== false) return false;
        }

        return true;
    }

    /**
     * Permet de récupérer un tableau des journées qui correspondent à ma réservation
     *
     * @return array Un tableau d'objets DateTimeImmutable représentant les jours de la réservation
     */
    public function getDays()
    {
        $resultat = range(
            $this->startDateAt->getTimestamp(),
            $this->endDateAt->getTimestamp(),
            24 * 60 * 60
        );

        $days =  array_map(function ($dayTimestamp) {
            return new \DateTimeImmutable(date('Y-m-d', $dayTimestamp));
        }, $resultat);

        return $days;
    }

    // /**
    //  * Dans le cas où le client souhaite deux nuits minimum au niveau de la résa
    //  *
    //  * @param ExecutionContextInterface $context
    //  * @param mixed $payload
    //  * @return void
    //  */
    // #[Assert\Callback()]
    // public function validateDuration(ExecutionContextInterface $context, mixed $payload): void
    // {
    //     $duration = $this->getDuration();
    //     if ($duration < 2) {
    //         $context->buildViolation('La réservation doit être d\'au moins deux nuits.')
    //             ->atPath('endDateAt')
    //             ->addViolation();
    //     }
    // }

    public function getDuration()
    {
        $diff = $this->endDateAt->diff($this->startDateAt);
        return $diff->days;
    }

    /**
     * Vérifie si les dates de réservation sont continues par rapport aux dates existantes
     *
     * @return boolean
     */
    public function areDatesContinuous(): bool
    {
        return $this->ad->areDatesContinuous($this->startDateAt, $this->endDateAt);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): static
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): static
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDateAt(): ?\DateTimeImmutable
    {
        return $this->startDateAt;
    }

    public function setStartDateAt(\DateTimeImmutable $startDateAt): static
    {
        $this->startDateAt = $startDateAt;

        return $this;
    }

    public function getEndDateAt(): ?\DateTimeImmutable
    {
        return $this->endDateAt;
    }

    public function setEndDateAt(\DateTimeImmutable $endDateAt): static
    {
        $this->endDateAt = $endDateAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setBooking($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getBooking() === $this) {
                $comment->setBooking(null);
            }
        }

        return $this;
    }
}
