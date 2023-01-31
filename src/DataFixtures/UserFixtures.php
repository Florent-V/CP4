<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public static int $userIndex = 0;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i=0; $i <= 50; $i++){
            self::$userIndex++;
            $user = new User();
            $user->setLastName($faker->lastName());
            $user->setFirstName($faker->firstName());
            $user->setEmail($faker->email());
            $manager->persist($user);
            $this->addReference('user_' . self::$userIndex, $user);
        }
        $manager->flush();
    }
}
