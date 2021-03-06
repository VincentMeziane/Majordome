<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use App\Entity\Traits\Timestampable;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="users")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"email"}, message="Cet email existe déjà")
 */
class User implements UserInterface
{
    use Timestampable;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email(message="Email incorrect")
     * @Assert\NotBlank(message="L'émail ne peut pas être vide")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Aucun prénom")
     * 
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Aucun nom")
     * 
     */
    private $lastName;

    /**
     * @ORM\OneToMany(targetEntity=Card::class, mappedBy="user", orphanRemoval=true)
     */
    private $cards;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="users")
     */
    private $subscription;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="subscription")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="subscriber", orphanRemoval=true)
     */
    private $unseenNotifications;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="author", orphanRemoval=true)
     */
    private $newNotification;

  
    public function __construct()
    {
        $this->cards = new ArrayCollection();
        $this->subscription = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->unseenNotifications = new ArrayCollection();
        $this->newNotification = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return Collection|Card[]
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    public function addCard(Card $card): self
    {
        if (!$this->cards->contains($card)) {
            $this->cards[] = $card;
            $card->setUser($this);
        }

        return $this;
    }

    public function removeCard(Card $card): self
    {
        if ($this->cards->contains($card)) {
            $this->cards->removeElement($card);
            // set the owning side to null (unless already changed)
            if ($card->getUser() === $this) {
                $card->setUser(null);
            }
        }

        return $this;
    }

    public function __toString() {
        $name = $this->firstName .' '. $this->lastName;
        return $name;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }


    /**
     * @return Collection|Notification[]
     */
    public function getUnseenNotifications(): Collection
    {
        return $this->unseenNotifications;
    }

    public function addUnseenNotification(Notification $unseenNotification): self
    {
        if (!$this->unseenNotifications->contains($unseenNotification)) {
            $this->unseenNotifications[] = $unseenNotification;
            $unseenNotification->setSubscriber($this);
        }

        return $this;
    }

    public function removeUnseenNotification(Notification $unseenNotification): self
    {
        if ($this->unseenNotifications->contains($unseenNotification)) {
            $this->unseenNotifications->removeElement($unseenNotification);
            // set the owning side to null (unless already changed)
            if ($unseenNotification->getSubscriber() === $this) {
                $unseenNotification->setSubscriber(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNewNotification(): Collection
    {
        return $this->newNotification;
    }

    public function addNewNotification(Notification $newNotification): self
    {
        if (!$this->newNotification->contains($newNotification)) {
            $this->newNotification[] = $newNotification;
            $newNotification->setAuthor($this);
        }

        return $this;
    }

    public function removeNewNotification(Notification $newNotification): self
    {
        if ($this->newNotification->contains($newNotification)) {
            $this->newNotification->removeElement($newNotification);
            // set the owning side to null (unless already changed)
            if ($newNotification->getAuthor() === $this) {
                $newNotification->setAuthor(null);
            }
        }

        return $this;
    }
    

}
