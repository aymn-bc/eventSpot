<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Enum\Categorie;
use App\Enum\StatutEvent;
use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    normalizationContext: ['groups' => ['event:read']],
    denormalizationContext: ['groups' => ['event:write']],
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete(),
    ]
)]
#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['event:read'])]
    private ?int $id = null;

    #[Groups(['event:read', 'event:write'])]
    #[Assert\NotBlank(message: 'Le titre ne peut pas être vide.')]
    #[Assert\Length(min: 5, minMessage: 'Le titre doit contenir au moins {{ limit }} caractères.')]
    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[Groups(['event:read', 'event:write'])]
    #[Assert\NotBlank(message: 'La description ne peut pas être vide.')]
    #[Assert\Length(min: 30, minMessage: 'La description doit contenir au moins {{ limit }} caractères.')]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[Groups(['event:read', 'event:write'])]
    #[ORM\Column]
    #[Assert\NotNull(message: 'La date de début est obligatoire.')]
    private ?\DateTime $dateDebut = null;

    #[Groups(['event:read', 'event:write'])]
    #[ORM\Column]
    #[Assert\NotNull(message: 'La date de fin est obligatoire.')]
    private ?\DateTime $dateFin = null;

    #[Groups(['event:read'])]
    #[Assert\NotBlank(message: 'Le lieu ne peut pas être vide.')]
    #[ORM\Column(length: 255)]
    private ?string $lieu = null;

    #[Groups(['event:read', 'event:write'])]
    #[ORM\Column]
    #[Assert\Range(min: 1, notInRangeMessage: "La capacité maximale doit être d'au moins {{ min }} personne.")]
    private ?int $capaciteMax = null;

    #[Groups(['event:read', 'event:write'])]
    #[ORM\Column]
    #[Assert\PositiveOrZero(message: "Le prix ne peut pas être négatif.")]
    private ?float $prix = null;

    #[Groups(['event:read', 'event:write'])]
    #[ORM\Column(length: 30, enumType: Categorie::class)]
    private ?Categorie $categorie = null;

    #[Groups(['event:read'])]
    #[ORM\Column(length: 20, enumType: StatutEvent::class)]
    private ?StatutEvent $status = null;

    #[ORM\Column]
    private ?\DateTime $dateCreation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageName = null;

    #[Groups(['event:read'])]
    #[ORM\ManyToOne(inversedBy: 'evenements')]
    private ?Lieu $lieu_event = null;

    /**
     * @var Collection<int, Inscription>
     */
    #[ORM\OneToMany(targetEntity: Inscription::class, mappedBy: 'evenement')]
    private Collection $inscriptions;

    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'evenement')]
    private Collection $organisateur;

    /**
     * @var Collection<int, TagEvenement>
     */
    #[Groups(['event:read'])]
    #[ORM\ManyToMany(targetEntity: TagEvenement::class, inversedBy: 'evenements')]
    private Collection $tagEvenements;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
        $this->tagEvenements = new ArrayCollection();
        $this->organisateur = new ArrayCollection();
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

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(Categorie $categorie): static
    {
        $this->categorie = $categorie;
        return $this;
    }

    public function getStatus(): ?StatutEvent
    {
        return $this->status;
    }

    public function setStatus(StatutEvent $status): static
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

    public function getTagEvenements(): Collection
    {
        return $this->tagEvenements;
    }

    public function addTagEvenement(TagEvenement $tagEvenement): static
    {
        if (!$this->tagEvenements->contains($tagEvenement)) {
            $this->tagEvenements->add($tagEvenement);
        }
        return $this;
    }

    public function removeTagEvenement(TagEvenement $tagEvenement): static
    {
        $this->tagEvenements->removeElement($tagEvenement);
        return $this;
    }
}