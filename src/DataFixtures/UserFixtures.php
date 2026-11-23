<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin'));
        $manager->persist($admin);

        $responsable = new User();
        $responsable->setUsername('responsable');
        $responsable->setRoles(['ROLE_RESPONSABLE']);
        $responsable->setPassword($this->hasher->hashPassword($responsable, 'responsable'));
        $manager->persist($responsable);

        $employe = new User();
        $employe->setUsername('employe');
        $employe->setRoles(['ROLE_EMPLOYE']);
        $employe->setPassword($this->hasher->hashPassword($employe, 'employe'));
        $manager->persist($employe);

        $manager->flush();
    }
}
