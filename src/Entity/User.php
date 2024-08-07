<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte avec cet email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, TwoFactorInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $authCode;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    private ?string $plainPassword = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    private ?string $adress = null;

    #[ORM\Column(length: 255)]
    private ?string $postalCode = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(type: 'phone_number')]
    #[AssertPhoneNumber(defaultRegion: 'FR')]
    private ?PhoneNumber $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetToken = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdTokenAt = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Avatar $avatar = null;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'booker')]
    private Collection $bookings;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, Ad>
     */
    #[ORM\OneToMany(targetEntity: Ad::class, mappedBy: 'author')]
    private Collection $ads;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $comments;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'host', orphanRemoval: true)]
    private Collection $hostMessages;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'guest', orphanRemoval: true)]
    private Collection $guestMessages;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->ads = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->hostMessages = new ArrayCollection();
        $this->guestMessages = new ArrayCollection();
    }

    public function __toString()
    {
        return "{$this->firstname} {$this->lastname}";
    }

    /**
     * Vérifie si l'utilisateur est auteur d'une annonce
     *
     * @return boolean
     */
    public function isAuthor(): bool
    {
        return !$this->ads->isEmpty();
    }

    public function getOldestAdYear(): ?int
    {
        $oldestAd = null;

        foreach ($this->ads as $ad) {
            if (null === $oldestAd || $ad->getCreatedAt() < $oldestAd->getCreatedAt()) {
                $oldestAd = $ad;
            }
        }

        return $oldestAd ? $oldestAd->getCreatedAt()->format('Y') : null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Retourne le nom complet de l'utilisateur
     *
     * @return string
     */
    public function getFullname(): string
    {
        return "{$this->firstname} {$this->lastname}";
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): static
    {
        $this->adress = $adress;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;

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

    public function getPhone(): ?PhoneNumber
    {
        return $this->phone;
    }

    public function setPhone(?PhoneNumber $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): static
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function getCreatedTokenAt(): ?\DateTimeImmutable
    {
        return $this->createdTokenAt;
    }

    public function setCreatedTokenAt(?\DateTimeImmutable $createdTokenAt): static
    {
        $this->createdTokenAt = $createdTokenAt;

        return $this;
    }

    public function isEmailAuthEnabled(): bool
    {
        return true; // This can be a persisted field to switch email code authentication on/off
    }

    public function getEmailAuthRecipient(): string
    {
        return $this->email;
    }

    public function getEmailAuthCode(): string
    {
        if (null === $this->authCode) {
            throw new \LogicException('The email authentication code was not set');
        }

        return $this->authCode;
    }

    public function setEmailAuthCode(string $authCode): void
    {
        $this->authCode = $authCode;
    }

    public function getAvatar(): ?Avatar
    {
        return $this->avatar;
    }

    public function setAvatar(?Avatar $avatar): static
    {
        // unset the owning side of the relation if necessary
        if ($avatar === null && $this->avatar !== null) {
            $this->avatar->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($avatar !== null && $avatar->getUser() !== $this) {
            $avatar->setUser($this);
        }

        $this->avatar = $avatar;

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
            $booking->setBooker($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getBooker() === $this) {
                $booking->setBooker(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Ad>
     */
    public function getAds(): Collection
    {
        return $this->ads;
    }

    public function addAd(Ad $ad): static
    {
        if (!$this->ads->contains($ad)) {
            $this->ads->add($ad);
            $ad->setAuthor($this);
        }

        return $this;
    }

    public function removeAd(Ad $ad): static
    {
        if ($this->ads->removeElement($ad)) {
            // set the owning side to null (unless already changed)
            if ($ad->getAuthor() === $this) {
                $ad->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * Récupère tous les commentaires d'un Hôte
     *
     * @return array
     */
    public function getAllComments(): array
    {
        $allComments = [];

        foreach ($this->ads as $ad) {
            foreach ($ad->getBookings() as $booking) {
                $allComments = array_merge($allComments, $booking->getComments()->toArray());
            }
        }

        return $allComments;
    }

    /**
     * Retourne la note globale de l'hôte
     *
     * @return void
     */
    public function getAvgRatingForHost()
    {
        $totalRating = 0;
        $totalComments = 0;

        foreach ($this->ads as $ad) {
            foreach ($ad->getBookings() as $booking) {
                foreach ($booking->getComments() as $comment) {
                    $totalRating += $comment->getRating();
                    $totalComments++;
                }
            }
        }

        if ($totalComments > 0) return $totalRating / $totalComments;
        return 0;
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
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getHostMessages(): Collection
    {
        return $this->hostMessages;
    }

    public function addHostMessage(Message $hostMessage): static
    {
        if (!$this->hostMessages->contains($hostMessage)) {
            $this->hostMessages->add($hostMessage);
            $hostMessage->setHost($this);
        }

        return $this;
    }

    public function removeHostMessage(Message $hostMessage): static
    {
        if ($this->hostMessages->removeElement($hostMessage)) {
            // set the owning side to null (unless already changed)
            if ($hostMessage->getHost() === $this) {
                $hostMessage->setHost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getGuestMessages(): Collection
    {
        return $this->guestMessages;
    }

    public function addGuestMessage(Message $guestMessage): static
    {
        if (!$this->guestMessages->contains($guestMessage)) {
            $this->guestMessages->add($guestMessage);
            $guestMessage->setGuest($this);
        }

        return $this;
    }

    public function removeGuestMessage(Message $guestMessage): static
    {
        if ($this->guestMessages->removeElement($guestMessage)) {
            // set the owning side to null (unless already changed)
            if ($guestMessage->getGuest() === $this) {
                $guestMessage->setGuest(null);
            }
        }

        return $this;
    }
}
