<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EvenementApiTest extends WebTestCase
{
    public function testGetEvenementsRetourne200(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/evenements', [], [], [
            'HTTP_ACCEPT' => 'application/ld+json',
        ]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testPostEvenementValideRetourne201(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/evenements', [], [], ['CONTENT_TYPE' => 'application/ld+json'],
            json_encode([
                'titre'       => 'Événement API Test',
                'description' => 'Description suffisamment longue pour passer la validation minimale.',
                'dateDebut'   => '2026-06-01T10:00:00',
                'dateFin'     => '2026-06-01T18:00:00',
                'lieu'        => 'Salle de test',
                'capaciteMax' => 100,
                'prix'        => 0.0,
                'categorie'   => 'meetup',
                'status'      => 'publie',
            ])
        );
        $this->assertResponseStatusCodeSame(201);
    }

    public function testPostEvenementTitreVideRetourne422(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/evenements', [], [], ['CONTENT_TYPE' => 'application/ld+json'],
            json_encode([
                'titre'       => '',
                'description' => 'Description valide suffisamment longue.',
                'dateDebut'   => '2026-06-01T10:00:00',
                'dateFin'     => '2026-06-01T18:00:00',
                'lieu'        => 'Salle de test',
                'lieu'        => 'Salle de test',
                'capaciteMax' => 100,
                'prix'        => 0.0,
                'categorie'   => 'meetup',
                'status'      => 'publie',
            ])
        );
        $this->assertResponseStatusCodeSame(422);
    }
}
