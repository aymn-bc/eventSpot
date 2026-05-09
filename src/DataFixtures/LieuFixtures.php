<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LieuFixtures extends Fixture
{
    public const LIEUX = [
        ['nom' => 'Centre de congrès', 'adresse' => '1 Place de la République', 'ville' => 'Paris', 'capacite' => 500],
        ['nom' => 'Salle polyvalente', 'adresse' => '25 Rue de la Paix', 'ville' => 'Lyon', 'capacite' => 200],
        ['nom' => 'Amphithéâtre universitaire', 'adresse' => '10 Avenue de l\'Université', 'ville' => 'Bordeaux', 'capacite' => 300],
        ['nom' => 'Espace coworking', 'adresse' => '5 Rue des Startups', 'ville' => 'Toulouse', 'capacite' => 100],
        ['nom' => 'Parc municipal', 'adresse' => '50 Boulevard du Parc', 'ville' => 'Marseille', 'capacite' => 1000],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::LIEUX as $index => $data) {
            $lieu = new Lieu();
            $lieu->setNom($data['nom']);
            $lieu->setAdresse($data['adresse']);
            $lieu->setVille($data['ville']);
            $lieu->setCapacite($data['capacite']);

            $manager->persist($lieu);
            $this->addReference('lieu_' . $index, $lieu);
        }

        $manager->flush();
    }
}
