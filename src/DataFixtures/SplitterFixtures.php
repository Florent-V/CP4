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
        self::$splitExpense++;
        $group = new Splitter();
        $group->setName('Splitter N°' . self::$splitExpense);
        $group->setDescription($faker->paragraph());
        $group->setUniqueId(md5(uniqid(strval(time()), true)));
        $group->setCategory($this->getReference(
            'splitterCategory_' .
            $faker->numberBetween(1, SplitterCategoryFixtures::$splitterCategoryIndex)
        ));
        $group->setOwner($this->getReference('member_11'));
        $group->addMember($this->getReference('member_11'));
        $group->addMember($this->getReference('member_21'));
        $group->addMember($this->getReference('member_31'));
        $group->addMember($this->getReference('member_41'));
        $manager->persist($group);
        $this->addReference('splitter_' . self::$splitExpense, $group);

        self::$splitExpense++;
        $group = new Splitter();
        $group->setName('Splitter N°' . self::$splitExpense);
        $group->setDescription($faker->paragraph());
        $group->setUniqueId(md5(uniqid(strval(time()), true)));
        $group->setCategory($this->getReference(
            'splitterCategory_' .
            $faker->numberBetween(1, SplitterCategoryFixtures::$splitterCategoryIndex)
        ));
        $group->setOwner($this->getReference('member_22'));
        $group->addMember($this->getReference('member_12'));
        $group->addMember($this->getReference('member_22'));
        $group->addMember($this->getReference('member_51'));
        $group->addMember($this->getReference('member_61'));
        $manager->persist($group);
        $this->addReference('splitter_' . self::$splitExpense, $group);

        self::$splitExpense++;
        $group = new Splitter();
        $group->setName('Splitter N°' . self::$splitExpense);
        $group->setDescription($faker->paragraph());
        $group->setUniqueId(md5(uniqid(strval(time()), true)));
        $group->setCategory($this->getReference(
            'splitterCategory_' .
            $faker->numberBetween(1, SplitterCategoryFixtures::$splitterCategoryIndex)
        ));
        $group->setOwner($this->getReference('member_32'));
        $group->addMember($this->getReference('member_42'));
        $group->addMember($this->getReference('member_52'));
        $group->addMember($this->getReference('member_62'));
        $group->addMember($this->getReference('member_32'));
        $manager->persist($group);
        $this->addReference('splitter_' . self::$splitExpense, $group);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            MemberFixtures::class,
            SplitterCategoryFixtures::class,
        ];
    }
}
