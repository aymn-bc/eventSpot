<?php

namespace App\DataFixtures;

use App\Entity\Evenement;
use App\Entity\Lieu;
use App\Entity\TagEvenement;
use App\Entity\User;
use App\Enum\Categorie;
use App\Enum\StatutEvent;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EvenementFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            LieuFixtures::class,
            UserFixtures::class,
            TagEvenementFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $statuts = StatutEvent::cases();

        $evenements = [
            ['titre' => 'Conférence sur l\'Intelligence Artificielle', 'categorie' => Categorie::CONFERENCE],
            ['titre' => 'Atelier développement web moderne', 'categorie' => Categorie::ATELIER],
            ['titre' => 'Meetup des entrepreneurs', 'categorie' => Categorie::MEETUP],
            ['titre' => 'Formation Symfony avancée', 'categorie' => Categorie::FORMATION],
            ['titre' => 'Concert de jazz en plein air', 'categorie' => Categorie::CONCERT],
            ['titre' => 'Hackathon Green Tech', 'categorie' => Categorie::ATELIER],
            ['titre' => 'Conférence cybersécurité 2026', 'categorie' => Categorie::CONFERENCE],
            ['titre' => 'Meetup startup founders', 'categorie' => Categorie::MEETUP],
            ['titre' => 'Formation Docker et Kubernetes', 'categorie' => Categorie::FORMATION],
            ['titre' => 'Festival de musique électronique', 'categorie' => Categorie::CONCERT],
            ['titre' => 'Atelier design thinking', 'categorie' => Categorie::ATELIER],
            ['titre' => 'Conférence blockchain', 'categorie' => Categorie::CONFERENCE],
            ['titre' => 'Meetup UX/UI designers', 'categorie' => Categorie::MEETUP],
            ['titre' => 'Formation React et TypeScript', 'categorie' => Categorie::FORMATION],
            ['titre' => 'Concert philharmonique', 'categorie' => Categorie::CONCERT],
        ];

        foreach ($evenements as $index => $data) {
            $evenement = new Evenement();
            $evenement->setTitre($data['titre']);
            $evenement->setDescription($faker->paragraphs(2, true));

            $dateDebut = $faker->dateTimeBetween('-3 months', '+6 months');
            $dateFin = (clone $dateDebut)->modify('+' . rand(1, 5) . ' hours');
            $evenement->setDateDebut($dateDebut);
            $evenement->setDateFin($dateFin);

            $evenement->setLieu($faker->address());
            $evenement->setCapaciteMax($faker->numberBetween(20, 300));
            $evenement->setPrix($faker->randomElement([0, 10, 20, 50, 100, 150]));
            $evenement->setCategorie($data['categorie']);
            $evenement->setStatus($faker->randomElement($statuts));
            $evenement->setDateCreation();

            // Lieu
            $evenement->setLieuEvent($this->getReference('lieu_' . rand(0, 4), Lieu::class));

            // Organisateur
            $evenement->setOrganisateur($this->getReference('user_orga_' . rand(0, 1), User::class));

            // Tags (1 à 4 aléatoires)
            $nbTags = rand(1, 4);
            $tagIndexes = array_rand(range(0, 7), $nbTags);
            if (!is_array($tagIndexes)) {
                $tagIndexes = [$tagIndexes];
            }
            foreach ($tagIndexes as $tagIndex) {
                $evenement->addTagEvenement($this->getReference('tag_' . $tagIndex, TagEvenement::class));
            }

            $manager->persist($evenement);
            $this->addReference('evenement_' . $index, $evenement);
        }

        $manager->flush();
    }
}