<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Admin
        $admin = new User();
        $admin->setEmail('admin@eventspot.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin123'));
        $admin->setPseudo('Admin');
        $manager->persist($admin);
        $this->addReference('user_admin', $admin);

        // Organisateurs
        foreach (['orga1', 'orga2'] as $index => $orga) {
            $organisateur = new User();
            $organisateur->setEmail($orga . '@eventspot.com');
            $organisateur->setRoles(['ROLE_ORGANISATEUR']);
            $organisateur->setPassword($this->hasher->hashPassword($organisateur, 'orga123'));
            $organisateur->setPseudo('Organisateur ' . ($index + 1));
            $manager->persist($organisateur);
            $this->addReference('user_orga_' . $index, $organisateur);
        }

        // Participants (Faker)
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->email());
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->hasher->hashPassword($user, 'user123'));
            $user->setPseudo($faker->userName());
            $manager->persist($user);
            $this->addReference('user_participant_' . $i, $user);
        }

        $manager->flush();
    }
}