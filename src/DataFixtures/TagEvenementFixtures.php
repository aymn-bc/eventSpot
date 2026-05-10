<?php

namespace App\DataFixtures;

use App\Entity\TagEvenement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagEvenementFixtures extends Fixture
{
    public const TAGS = [
        ['nom' => 'Networking', 'couleur' => '#3498db'],
        ['nom' => 'Tech', 'couleur' => '#2ecc71'],
        ['nom' => 'Gratuit', 'couleur' => '#27ae60'],
        ['nom' => 'Startup', 'couleur' => '#e74c3c'],
        ['nom' => 'Formation', 'couleur' => '#9b59b6'],
        ['nom' => 'Culture', 'couleur' => '#f39c12'],
        ['nom' => 'Sport', 'couleur' => '#1abc9c'],
        ['nom' => 'Famille', 'couleur' => '#e67e22'],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::TAGS as $index => $data) {
            $tag = new TagEvenement();
            $tag->setNom($data['nom']);
            $tag->setCouleur($data['couleur']);

            $manager->persist($tag);
            $this->addReference('tag_' . $index, $tag);
        }

        $manager->flush();
    }
}