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
    public static int $splitExpense = 0;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 1; $i <= UserFixtures::$userIndex; $i++) {
            self::$splitExpense++;
            $group = new Splitter();
            $group->setName('Splitter N°' . self::$groupIndex);
            $group->setDescription($faker->paragraph());
            $group->setUniqueId(md5(uniqid(strval(time()), true)));
            $group->setCategory($this->getReference(
                'splitterCategory_' .
                $faker->numberBetween(1, SplitterCategoryFixtures::$splitterCategoryIndex)
            ));
            $group->setOwnedBy($this->getReference('user_' . $i));
            $manager->persist($group);
            $this->addReference('splitter_' . self::$groupIndex, $group);
        }

        foreach (self::SPLITTERS as $splitter) {
            self::$splitExpense++;
            $group = new Splitter();
            $group->setName('Expense Splitter N°' . self::$splitExpense);
            $group->setDescription($faker->paragraph());
            $group->setUniqueId(md5(uniqid(strval(time()), true)));
            $group->setCategory($this->getReference(
                'splitterCategory_' .
                $faker->numberBetween(1, SplitterCategoryFixtures::$splitterCategoryIndex)
            ));
            $group->setOwnedBy($this->getReference('user_' . ($splitter[0] - 1)));
            $manager->persist($group);
            $this->addReference('splitter_' . self::$splitExpense, $group);
        }

        for ($i = 0; $i <= 30; $i++) {
            self::$groupIndex++;
            $group = new Splitter();
            $group->setName('Splitter (bis) N°' . self::$groupIndex);
            $group->setDescription($faker->paragraph());
            $group->setUniqueId(md5(uniqid(strval(time()), true)));
            $group->setCategory($this->getReference(
                'splitterCategory_' .
                $faker->numberBetween(1, SplitterCategoryFixtures::$splitterCategoryIndex)
            ));
            $group->setOwnedBy($this->getReference(
                'user_' .
                $faker->unique()->numberBetween(1, UserFixtures::$userIndex)
            ));
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
            SplitterCategoryFixtures::class,
        ];
    }
}
