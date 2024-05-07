<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    protected $slugger;
    protected $passwordHasher;

    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $passwordHasher)
    {
        $this->slugger = $slugger;
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $admin = new User;

        $hash = $this->passwordHasher->hashPassword($admin, "password");
        $admin->setFirstname('admin')
            ->setLastname('admin')
            ->setPassword($hash)
            ->setRoles(['ROLE_ADMIN'])
            ->setAdress($faker->streetAddress)
            ->setPostalCode($faker->postcode)
            ->setCity($faker->city)
            ->setEmail("admin@gmail.com")
            ->setPhone($faker->mobileNumber());

        $manager->persist($admin);

        for ($u = 0; $u < 5; $u++) {
            $user = new User;
            $hash = $this->passwordHasher->hashPassword($user, "password");
            $user->setEmail("user$u@gmail.com")
                ->setFirstname($faker->firstName())
                ->setLastname($faker->lastName())
                ->setPassword($hash)
                ->setAdress($faker->streetAddress)
                ->setPostalCode($faker->postcode)
                ->setCity($faker->city)
                ->setPhone($faker->mobileNumber());

            $manager->persist($user);
        }

        for ($i = 0; $i < 10; $i++) {
            $ad = new Ad;
            $ad->setName($faker->sentence())
                ->setSlug($this->slugger->slug($ad->getName()))
                ->setIntroduction($faker->paragraph(2))
                ->setContent($faker->paragraph())
                ->setRooms($faker->numberBetween(1, 6))
                ->setCapacity($faker->numberBetween(2, 10))
                ->setPrice($faker->numberBetween(25000, 100000));

            $manager->persist($ad);
        }

        $manager->flush();
    }
}
