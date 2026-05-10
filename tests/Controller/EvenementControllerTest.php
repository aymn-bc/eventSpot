<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EvenementControllerTest extends WebTestCase
{
    public function testListeEvenementsRetourne200(): void
    {
        $client = static::createClient();
        $client->request('GET', '/evenements');
        $this->assertResponseStatusCodeSame(200);
    }

    public function testAccueilContientEvenements(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseStatusCodeSame(200);
    }

    public function testNouveauEvenementInterditSansAuthentification(): void
    {
        $client = static::createClient();
        $client->request('GET', '/evenements/nouveau');
        $this->assertResponseStatusCodeSame(302);
    }

    public function testCreationEvenementAvecAuthentification(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'orga1@eventspot.com']);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/evenements/nouveau');
        $form = $crawler->selectButton('Créer l\'événement')->form([
            'evenement[titre]'       => 'Événement Test PHPUnit',
            'evenement[description]' => 'Description suffisamment longue pour passer la validation minimale.',
            'evenement[dateDebut]'   => '2026-06-01T10:00',
            'evenement[dateFin]'     => '2026-06-01T18:00',
            'evenement[lieu]'        => 'Salle de test',
            'evenement[capaciteMax]' => '50',
            'evenement[prix]'        => '0',
            'evenement[categorie]'   => '2',
            'evenement[status]'      => '1',
        ]);

        $client->submit($form);
        $this->assertResponseRedirects('/evenements');
        $client->followRedirect();
        $this->assertSelectorExists('.alert-success');
    }
}
