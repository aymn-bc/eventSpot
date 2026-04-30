<?php

namespace App\Entity;

use App\Enum\Categorie;
use App\Enum\StatutEvent;
use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Le titre ne peut pas être vide.')]
    #[Assert\Length(min: 5,minMessage: 'Le titre doit contenir au moins {{ limit }} caractères.')]
    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[Assert\NotBlank(message: 'La description ne peut pas être vide.')]
    #[Assert\Length(min: 30,minMessage: 'La description doit contenir au moins {{ limit }} caractères.')]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'La date de debut est obligatoire.')]
    private ?\DateTime $dateDebut = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'La date de debut est obligatoire.')]
    private ?\DateTime $dateFin = null;

    #[Assert\NotBlank(message: 'Le lieu ne peut pas être vide.')]
    #[ORM\Column(length: 255)]
    private ?string $lieu = null;

    #[ORM\Column]
    #[Assert\Range(min: 1, notInRangeMessage: "La capacité maximale doit être d'au moins {{ min }} personne.")] 
    private ?int $capaciteMax = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero(message: "Le prix ne peut pas être négatif.")]
    private ?float $prix = null;

    #[ORM\Column(length: 30, enumType: Categorie::class)]
    private ?string $categorie = null;

    #[ORM\Column(length: 20, enumType: StatutEvent::class)]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTime $dateCreation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageName = null;

    #[ORM\ManyToOne(inversedBy: 'evenements')]
    private ?Lieu $lieu_event = null;

    /**
     * @var Collection<int, Inscription>
     */
    #[ORM\OneToMany(targetEntity: Inscription::class, mappedBy: 'evenement')]
    private Collection $inscriptions;

    #[ORM\ManyToOne(inversedBy: 'evenements')]
    private ?User $organisateur = null;

    /**
     * @var Collection<int, TagEvenemement>
     */
    #[ORM\ManyToMany(targetEntity: TagEvenemement::class, inversedBy: 'evenements')]
    private Collection $tagEvenements;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
        $this->tagEvenements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDateDebut(): ?\DateTime
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTime $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTime
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTime $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getCapaciteMax(): ?int
    {
        return $this->capaciteMax;
    }

    public function setCapaciteMax(int $capaciteMax): static
    {
        $this->capaciteMax = $capaciteMax;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): static
    {
        $this->categorie = $categorie;

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

    public function getDateCreation(): ?\DateTime
    {
        return $this->dateCreation;
    }

    public function setDateCreation(): static
    {
        $this->dateCreation = new \DateTime();

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): static
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getLieuEvent(): ?Lieu
    {
        return $this->lieu_event;
    }

    public function setLieuEvent(?Lieu $lieu_event): static
    {
        $this->lieu_event = $lieu_event;

        return $this;
    }

    /**
     * @return Collection<int, Inscription>
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Inscription $inscription): static
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions->add($inscription);
            $inscription->setEvenement($this);
        }

        return $this;
    }

    public function removeInscription(Inscription $inscription): static
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getEvenement() === $this) {
                $inscription->setEvenement(null);
            }
        }

        return $this;
    }

    public function getOrganisateur(): ?User
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?User $organisateur): static
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    /**
     * @return Collection<int, TagEvenemement>
     */
    public function getTagEvenements(): Collection
    {
        return $this->tagEvenements;
    }

    public function addTagEvenement(TagEvenemement $tagEvenement): static
    {
        if (!$this->tagEvenements->contains($tagEvenement)) {
            $this->tagEvenements->add($tagEvenement);
        }

        return $this;
    }

    public function removeTagEvenement(TagEvenemement $tagEvenement): static
    {
        $this->tagEvenements->removeElement($tagEvenement);

        return $this;
    }
}
