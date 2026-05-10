<?php

namespace App\DataFixtures;

use App\Entity\Evenement;
use App\Entity\Inscription;
use App\Enum\StatutInscription;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class InscriptionFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            EvenementFixtures::class,
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $statuts = StatutInscription::cases();
        $count = 0;

        for ($i = 0; $i < 15 && $count < 30; $i++) {
            $evenement = $this->getReference('evenement_' . $i, Evenement::class);
            $capaciteMax = $evenement->getCapaciteMax();
            $nbInscriptions = min(rand(1, 5), $capaciteMax);

            for ($j = 0; $j < $nbInscriptions && $count < 30; $j++) {
                $inscription = new Inscription();
                $inscription->setEvenement($evenement);
                $inscription->setDateInscription();
                $inscription->setStatus($faker->randomElement($statuts));
                $inscription->setCommentaire($faker->optional(0.5)->sentence());

                $manager->persist($inscription);
                $count++;
            }
        }

        $manager->flush();
    }
}