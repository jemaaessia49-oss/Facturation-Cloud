<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Utilisateur Administrateur
        $admin = new User();
        $admin->setEmail('admin@facturation-cloud.local');
        $admin->setPrenom('Admin');
        $admin->setNom('Principal');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, 'admin1234')
        );
        $manager->persist($admin);

        // Utilisateur Consultant
        $consultant = new User();
        $consultant->setEmail('consultant@facturation-cloud.local');
        $consultant->setPrenom('Sara');
        $consultant->setNom('Consultante');
        $consultant->setRoles(['ROLE_CONSULTANT']);
        $consultant->setPassword(
            $this->passwordHasher->hashPassword($consultant, 'consultant1234')
        );
        $manager->persist($consultant);

        $manager->flush();
    }
}