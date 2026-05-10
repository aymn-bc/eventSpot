<?php

namespace App\Service;

use App\Entity\Evenement;
use App\Entity\User;
use App\Repository\EvenementRepository;
use App\Repository\InscriptionRepository;

class EvenementManager
{
    public function __construct(
        private InscriptionRepository $inscRepo,
        private EvenementRepository $eventRepo
    ) {}

    public function getPlacesRestantes(Evenement $e): int
    {
        $inscrits = $this->inscRepo->countByEvenement($e);
        return max(0, $e->getCapaciteMax() - $inscrits);
    }

    public function estInscrit(User $u, Evenement $e): bool
    {
        return $this->inscRepo->findByEvenementAndUser($e, $u) !== null;
    }

    public function getNbInscrits(Evenement $e): int
    {
        return $this->inscRepo->countByEvenement($e);
    }

    public function getEvenementsParCategorie(): array
    {
        $evenements = $this->eventRepo->findAll();
        $result = [];
        foreach ($evenements as $evenement) {
            $categorie = $evenement->getCategorie()?->value ?? 'autre';
            $result[$categorie][] = $evenement;
        }
        return $result;
    }
}