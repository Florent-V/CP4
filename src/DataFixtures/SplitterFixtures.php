<?php

namespace App\DataFixtures;

use App\Entity\Splitter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SplitterFixtures extends Fixture implements DependentFixtureInterface
{
    public static int $groupIndex = 0;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i <= 30; $i++) {
            self::$groupIndex++;
            $group = new Splitter();
            $group->setName('Splitter N°' . self::$groupIndex);
            $group->setDescription($faker->paragraph());
            $group->setCategory($this->getReference(
                'splitterCategory_' .
                $faker->numberBetween(1, SplitterCategoryFixtures::$splitterCategoryIndex)
            ));
            $group->setOwnedBy($this->getReference(
                'user_' .
                $faker->unique()->numberBetween(1, UserFixtures::$userIndex)
            ));
            for ($j = 1; $j <= 5; $j++) {
                $group->addMember($this->getReference(
                    'user_' .
                    $faker->unique()->numberBetween(1, UserFixtures::$userIndex)
                ));
            }
            $group->addMember($group->getOwnedBy());
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
