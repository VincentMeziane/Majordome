<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NotificationRepository::class)
 * @ORM\Table(name="notifications")
 */
class Notification
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $unseen;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="unseenNotifications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $subscriber;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="newNotification")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUnseen(): ?int
    {
        return $this->unseen;
    }

    public function setUnseen(?int $unseen): self
    {
        $this->unseen = $unseen;

        return $this;
    }

    public function getSubscriber(): ?User
    {
        return $this->subscriber;
    }

    public function setSubscriber(?User $subscriber): self
    {
        $this->subscriber = $subscriber;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function __toString() {

        $string = strval($this->getAuthor());
        return $string;
    }
}
