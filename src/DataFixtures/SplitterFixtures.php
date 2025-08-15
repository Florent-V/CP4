<?php

namespace App\DataFixtures;

use App\Entity\AppUser;
use App\Entity\Member;
use App\Entity\Splitter;
use App\Entity\SplitterCategory;
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
        $group->setName('Groupe N°' . self::$splitExpense);
        $group->setDescription($faker->paragraph());
        $group->setUniqueId(md5(uniqid(strval(time()), true)));
        $group->setCategory($this->getReference(
            'splitterCategory_' .
            $faker->numberBetween(1, SplitterCategoryFixtures::$splitCategoryIndex),
            SplitterCategory::class
        ));
        $group->addMember($this->getReference('member_11', Member::class));
        $group->addMember($this->getReference('member_21', Member::class));
        $group->addMember($this->getReference('member_31', Member::class));
        $group->addMember($this->getReference('member_41', Member::class));
        $group->addFavoritedByUser($this->getReference('appUser_1', AppUser::class));
        $group->addFavoritedByUser($this->getReference('appUser_2', AppUser::class));
        $group->addFavoritedByUser($this->getReference('appUser_3', AppUser::class));
        $group->addFavoritedByUser($this->getReference('appUser_4', AppUser::class));
        $group->setOwner($this->getReference('appUser_1', AppUser::class));
        $manager->persist($group);
        $this->addReference('splitter_' . self::$splitExpense, $group);

        self::$splitExpense++;
        $group = new Splitter();
        $group->setName('Groupe N°' . self::$splitExpense);
        $group->setDescription($faker->paragraph());
        $group->setUniqueId(md5(uniqid(strval(time()), true)));
        $group->setCategory($this->getReference(
            'splitterCategory_' .
            $faker->numberBetween(1, SplitterCategoryFixtures::$splitCategoryIndex),
            SplitterCategory::class
        ));
        $group->addMember($this->getReference('member_12', Member::class));
        $group->addMember($this->getReference('member_22', Member::class));
        $group->addMember($this->getReference('member_51', Member::class));
        $group->addMember($this->getReference('member_61', Member::class));
        $group->addFavoritedByUser($this->getReference('appUser_1', AppUser::class));
        $group->addFavoritedByUser($this->getReference('appUser_2', AppUser::class));
        $group->addFavoritedByUser($this->getReference('appUser_5', AppUser::class));
        $group->addFavoritedByUser($this->getReference('appUser_6', AppUser::class));
        $group->setOwner($this->getReference('appUser_2', AppUser::class));
        $manager->persist($group);
        $this->addReference('splitter_' . self::$splitExpense, $group);

        self::$splitExpense++;
        $group = new Splitter();
        $group->setName('Groupe N°' . self::$splitExpense);
        $group->setDescription($faker->paragraph());
        $group->setUniqueId(md5(uniqid(strval(time()), true)));
        $group->setCategory($this->getReference(
            'splitterCategory_' .
            $faker->numberBetween(1, SplitterCategoryFixtures::$splitCategoryIndex),
            SplitterCategory::class
        ));
        $group->addMember($this->getReference('member_42', Member::class));
        $group->addMember($this->getReference('member_52', Member::class));
        $group->addMember($this->getReference('member_62', Member::class));
        $group->addMember($this->getReference('member_32', Member::class));
        $group->addFavoritedByUser($this->getReference('appUser_3', AppUser::class));
        $group->addFavoritedByUser($this->getReference('appUser_4', AppUser::class));
        $group->addFavoritedByUser($this->getReference('appUser_5', AppUser::class));
        $group->addFavoritedByUser($this->getReference('appUser_6', AppUser::class));
        $group->setOwner($this->getReference('appUser_3', AppUser::class));
        $manager->persist($group);
        $this->addReference('splitter_' . self::$splitExpense, $group);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            MemberFixtures::class,
            UserFixtures::class,
            SplitterCategoryFixtures::class,
        ];
    }
}
