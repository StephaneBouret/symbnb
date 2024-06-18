<?php

namespace App\Entity;

use App\Repository\AdRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AdRepository::class)]
class Ad
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 50,
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column(nullable: true)]
    private ?int $price = null;

    #[ORM\Column(nullable: true)]
    #[Assert\GreaterThanOrEqual(1, message: "Vous devez avoir au moins 1 voyageur")]
    private ?int $capacity = null;

    #[ORM\Column(nullable: true)]
    #[Assert\GreaterThanOrEqual(1, message: "Vous devez avoir au moins 1 chambre")]
    private ?int $rooms = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(
        max: 5000,
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $content = null;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Images>
     */
    #[ORM\OneToMany(targetEntity: Images::class, mappedBy: 'ad', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $images;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'ad')]
    private Collection $bookings;

    #[ORM\Column(nullable: true)]
    #[Assert\GreaterThanOrEqual(1, message: "Vous devez avoir au moins 1 lit")]
    private ?int $beds = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $country = null;

    /**
     * @var Collection<int, Equipment>
     */
    #[ORM\ManyToMany(targetEntity: Equipment::class, mappedBy: 'ads')]
    private Collection $equipment;

    #[ORM\ManyToOne(inversedBy: 'ads')]
    private ?User $author = null;

    #[ORM\ManyToOne(inversedBy: 'ads')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Type $type = null;

    #[ORM\Column(nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(nullable: true)]
    private ?float $longitude = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $state = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $postalCode = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->images = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->equipment = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Permet d'obtenir un tableau des jours qui ne sont pas disponibles pour cette annonce
     *
     * @return array Un tableau d'objets DateTimeImmutable représentant les jours d'occupation
     */
    public function getNotAvailableDays()
    {
        $notAvailableDays = [];

        foreach ($this->bookings as $booking) {
            // Calculer les jours qui se trouvent entre la date d'arrivée et de départ
            $resultat = range(
                $booking->getStartDateAt()->getTimestamp(),
                $booking->getEndDateAt()->getTimestamp(),
                24 * 60 * 60
            );

            $days = array_map(function ($dayTimestamp) {
                return new \DateTimeImmutable(date('Y-m-d', $dayTimestamp));
            }, $resultat);

            $notAvailableDays = array_merge($notAvailableDays, $days);
        }

        return $notAvailableDays;
    }

    /**
     * Vérifie si les dates de réservation sont continues par rapport aux dates existantes
     *
     * @param \DateTimeImmutable $startDateAt
     * @param \DateTimeImmutable $endDateAt
     * @return boolean
     */
    public function areDatesContinuous(\DateTimeImmutable $startDateAt, \DateTimeImmutable $endDateAt): bool
    {
        $notAvailableDays = $this->getNotAvailableDays();
        foreach ($notAvailableDays as $day) {
            if ($day < $endDateAt && $day > $startDateAt) {
                return false;
            }
        }
        return true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms): static
    {
        $this->rooms = $rooms;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

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

    /**
     * @return Collection<int, Images>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setAd($this);
        }

        return $this;
    }

    public function removeImage(Images $image): static
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getAd() === $this) {
                $image->setAd(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setAd($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getAd() === $this) {
                $booking->setAd(null);
            }
        }

        return $this;
    }

    public function getBeds(): ?int
    {
        return $this->beds;
    }

    public function setBeds(int $beds): static
    {
        $this->beds = $beds;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return Collection<int, Equipment>
     */
    public function getEquipment(): Collection
    {
        return $this->equipment;
    }

    public function addEquipment(Equipment $equipment): static
    {
        if (!$this->equipment->contains($equipment)) {
            $this->equipment->add($equipment);
            $equipment->addAd($this);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): static
    {
        if ($this->equipment->removeElement($equipment)) {
            $equipment->removeAd($this);
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(?string $adress): static
    {
        $this->adress = $adress;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }
}
