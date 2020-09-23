<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\CardRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=CardRepository::class)
 * @ORM\Table(name="cards")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 */
class Card
{
    use Timestampable;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Aucun titre")
     * @Assert\Length(min=3, minMessage="Le titre doit contenir au moins 3 caractères")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Aucune description")
     * @Assert\Length(min=10, minMessage="La description doit contenir au moins 10 caractères")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageName;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="card_image", fileNameProperty="imageName")
     * @Assert\Image(maxSize="8M", maxSizeMessage="Le fichier est trop volumineux ({{ size }}{{ suffix }}). La taille maximale autorisée est de {{ limit }} {{ suffix }}", mimeTypes="image/jpeg")
     * @var File|null
     */
    private $imageFile;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->setUpdatedAt(new \DateTimeImmutable());
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }
}
