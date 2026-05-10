<?php

namespace App\Tests\Service;

use App\Entity\Evenement;
use App\Entity\Inscription;
use App\Entity\User;
use App\Repository\EvenementRepository;
use App\Repository\InscriptionRepository;
use App\Service\EvenementManager;
use PHPUnit\Framework\TestCase;

class EvenementManagerTest extends TestCase
{
    private InscriptionRepository $inscRepo;
    private EvenementRepository $eventRepo;
    private EvenementManager $manager;

    protected function setUp(): void
    {
        $this->inscRepo = $this->createMock(InscriptionRepository::class);
        $this->eventRepo = $this->createMock(EvenementRepository::class);
        $this->manager = new EvenementManager($this->inscRepo, $this->eventRepo);
    }

    public function testGetPlacesRestantesRetourneBonneValeur(): void
    {
        $evenement = $this->createMock(Evenement::class);
        $evenement->method('getCapaciteMax')->willReturn(100);
        $this->inscRepo->method('countByEvenement')->with($evenement)->willReturn(40);
        $this->assertEquals(60, $this->manager->getPlacesRestantes($evenement));
    }

    public function testGetPlacesRestantesRetourneZeroQuandComplet(): void
    {
        $evenement = $this->createMock(Evenement::class);
        $evenement->method('getCapaciteMax')->willReturn(50);
        $this->inscRepo->method('countByEvenement')->with($evenement)->willReturn(50);
        $this->assertEquals(0, $this->manager->getPlacesRestantes($evenement));
    }

    public function testGetPlacesRestantesNeRetournePasNegatif(): void
    {
        $evenement = $this->createMock(Evenement::class);
        $evenement->method('getCapaciteMax')->willReturn(10);
        $this->inscRepo->method('countByEvenement')->willReturn(15);
        $this->assertEquals(0, $this->manager->getPlacesRestantes($evenement));
    }

    public function testEstInscritRetourneTrueSiInscrit(): void
    {
        $user = $this->createMock(User::class);
        $evenement = $this->createMock(Evenement::class);
        $this->inscRepo->method('findByEvenementAndUser')->with($evenement, $user)->willReturn(new Inscription());
        $this->assertTrue($this->manager->estInscrit($user, $evenement));
    }

    public function testEstInscritRetourneFalseSiNonInscrit(): void
    {
        $user = $this->createMock(User::class);
        $evenement = $this->createMock(Evenement::class);
        $this->inscRepo->method('findByEvenementAndUser')->with($evenement, $user)->willReturn(null);
        $this->assertFalse($this->manager->estInscrit($user, $evenement));
    }

    public function testGetNbInscritsRetourneLeNombreCorrect(): void
    {
        $evenement = $this->createMock(Evenement::class);
        $this->inscRepo->method('countByEvenement')->with($evenement)->willReturn(25);
        $this->assertEquals(25, $this->manager->getNbInscrits($evenement));
    }

    public function testGetNbInscritsRetourneZeroSansInscription(): void
    {
        $evenement = $this->createMock(Evenement::class);
        $this->inscRepo->method('countByEvenement')->with($evenement)->willReturn(0);
        $this->assertEquals(0, $this->manager->getNbInscrits($evenement));
    }
}
