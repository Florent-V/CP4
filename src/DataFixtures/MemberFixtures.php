<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MemberFixtures extends Fixture implements DependentFixtureInterface
{
    public static int $memberIndex = 0;
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i <= SplitterFixtures::; $i++) {
            self::$memberIndex++;
            $member = new Member();
            $member->setNickname($faker->firstName());
            $member->setSplitter($this->getReference(
                'splitter_' .
                $faker->numberBetween(1, SplitterFixtures::$splitterIndex)
            ));
            $member->setUser($this->getReference(
                'user_' .
                $faker->numberBetween(1, UserFixtures::$userIndex)
            ));
            $manager->persist($member);
        }

        $member = new Member();
        $member->setNickname($faker->firstName());



        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SplitterFixtures::class
        ];
    }
}
