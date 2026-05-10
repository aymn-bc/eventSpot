<?php

namespace App\Entity;

use App\Enum\StatutInscription;
use App\Repository\InscriptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InscriptionRepository::class)]
class Inscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $dateInscription = null;

    #[ORM\Column(length: 15, enumType: StatutInscription::class)]
    private ?StatutInscription $status = null;

    #[Assert\Length(max: 500, maxMessage: 'Le commentaire ne peut pas dépasser {{ limit }} caractères.')]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\ManyToOne(inversedBy: 'inscriptions')]
    private ?Evenement $evenement = null;

    #[ORM\ManyToOne(inversedBy: 'inscriptions')]
    private ?User $participant = null;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateInscription(): ?\DateTime
    {
        return $this->dateInscription;
    }

    public function setDateInscription(): static
    {
        $this->dateInscription = new \DateTime();
        return $this;
    }

    public function getStatus(): ?StatutInscription
    {
        return $this->status;
    }

    public function setStatus(StatutInscription $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getEvenement(): ?Evenement
    {
        return $this->evenement;
    }

    public function setEvenement(?Evenement $evenement): static
    {
        $this->evenement = $evenement;
        return $this;
    }

    public function getParticipant(): ?User
    {
        return $this->participant;
    }

    public function setParticipant(?User $participant): static
    {
        $this->participant = $participant;
        return $this;
    }
}