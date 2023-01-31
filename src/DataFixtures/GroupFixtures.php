<?php

namespace App\DataFixtures;

use App\Entity\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class GroupFixtures extends Fixture implements DependentFixtureInterface
{
    public static int $groupIndex = 0;
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i <= 50; $i++){
            self::$groupIndex++;
            $group = new Group();
            $group->setName('Groupe N°' . self::$groupIndex);
            $group->setCreator($this->getReference('user_' . $faker->numberBetween(1, UserFixtures::$userIndex)));
            for ($j = 0; $j <= 10; $j++) {
                $group->addMember($this->getReference('user_' . $faker->unique()->numberBetween(1, UserFixtures::$userIndex)));
            }
            $faker->unique(true);
            $manager->persist($group);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
