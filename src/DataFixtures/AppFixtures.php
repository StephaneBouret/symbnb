<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Booking;
use Faker\Factory;
use App\Entity\User;
use DateTimeImmutable;
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

        $users = [];
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
            $users[] = $user;
        }

        for ($i = 0; $i < 10; $i++) {
            $ad = new Ad;
            $ad->setName($faker->sentence())
                ->setSlug($this->slugger->slug($ad->getName()))
                ->setContent($faker->paragraph())
                ->setRooms($faker->numberBetween(1, 6))
                ->setBeds($faker->numberBetween(1, 4))
                ->setCapacity($faker->numberBetween(2, 10))
                ->setCity($faker->city())
                ->setCountry($faker->country())
                ->setPrice($faker->numberBetween(25000, 100000));

            // Gestion des réservations
            for ($j = 0; $j < mt_rand(0, 3); $j++) {
                $booking = new Booking;
                $createdAt = DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months'));
                $startDateAt = DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-3 months'));
                // Gestion de la date de fin
                $duration  = mt_rand(3, 10);
                $endDateAt = (clone $startDateAt)->modify("+$duration days");
                $amount = $ad->getPrice() * $duration;
                $booker = $users[mt_rand(0, count($users) - 1)];

                $booking->setAd($ad)
                    ->setBooker($booker)
                    ->setCreatedAt($createdAt)
                    ->setStartDateAt($startDateAt)
                    ->setEndDateAt($endDateAt)
                    ->setAmount($amount);

                if ($faker->boolean(90)) {
                    $booking->setStatus(Booking::STATUS_PENDING);
                }
                $manager->persist($booking);
            }

            $manager->persist($ad);
        }

        $manager->flush();
    }
}
