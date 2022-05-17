<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 * @Vich\Uploadable
 */
#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $Titre;

    #[ORM\Column(type: 'string', length: 255)]
    private $image;

    #[ORM\Column(type: 'text')]
    private $description;

    #[ORM\Column(type: 'datetime')]
    private $datedAt;

    #[ORM\Column(type: 'string', length: 255)]
    private $Location;

    #[ORM\Column(type: 'boolean')]
    private $state;

    #[ORM\Column(type: 'integer')]
    private $place;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private $categorie;

    /**
     * @Vich\UploadableField(mapping="events", fileNameProperty="image")
     */
    private $imageFile;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'events')]
    private $user;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Demande::class)]
    private $demandes;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: AjoutEvent::class)]
    private $ajoutEvents;

    public function __construct()
    {
        $this->demandes = new ArrayCollection();
        $this->ajoutEvents = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->Titre;
    }

    public function setTitre(string $Titre): self
    {
        $this->Titre = $Titre;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDatedAt(): ?\DateTimeInterface
    {
        return $this->datedAt;
    }

    public function setDatedAt(\DateTimeInterface $datedAt): self
    {
        $this->datedAt = $datedAt;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->Location;
    }

    public function setLocation(string $Location): self
    {
        $this->Location = $Location;

        return $this;
    }

    public function isState(): ?bool
    {
        return $this->state;
    }

    public function setState(bool $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getPlace(): ?int
    {
        return $this->place;
    }

    public function setPlace(int $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getCategorie(): ?Category
    {
        return $this->categorie;
    }

    public function setCategorie(?Category $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }
    public function setImageFile(?File $imageFile = null): self
    {
        $this->imageFile = $imageFile;

        if ($this->imageFile instanceof UploadedFile) {
            $this->updated_at = new \DateTime('now');
        }
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
    public function __toString(){
        return $this->Titre;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Demande>
     */
    public function getDemandes(): Collection
    {
        return $this->demandes;
    }

    public function addDemande(Demande $demande): self
    {
        if (!$this->demandes->contains($demande)) {
            $this->demandes[] = $demande;
            $demande->setEvent($this);
        }

        return $this;
    }

    public function removeDemande(Demande $demande): self
    {
        if ($this->demandes->removeElement($demande)) {
            // set the owning side to null (unless already changed)
            if ($demande->getEvent() === $this) {
                $demande->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AjoutEvent>
     */
    public function getAjoutEvents(): Collection
    {
        return $this->ajoutEvents;
    }

    public function addAjoutEvent(AjoutEvent $ajoutEvent): self
    {
        if (!$this->ajoutEvents->contains($ajoutEvent)) {
            $this->ajoutEvents[] = $ajoutEvent;
            $ajoutEvent->setEvent($this);
        }

        return $this;
    }

    public function removeAjoutEvent(AjoutEvent $ajoutEvent): self
    {
        if ($this->ajoutEvents->removeElement($ajoutEvent)) {
            // set the owning side to null (unless already changed)
            if ($ajoutEvent->getEvent() === $this) {
                $ajoutEvent->setEvent(null);
            }
        }

        return $this;
    }
}
