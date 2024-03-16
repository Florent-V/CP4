<?php

namespace App\DataFixtures;

use App\Entity\Member;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class MemberFixtures extends Fixture implements DependentFixtureInterface
{
    public static int $memberIndex = 0;
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 1; $i <= SplitterFixtures::$splitExpense; $i++) {
            for ($j = 1; $j <= 4; $j++) {
                self::$memberIndex++;
                $member = new Member();
                $member->setNickname($faker->firstName());
                $member->setSplitter($this->getReference('splitter_' . $i));
                $manager->persist($member);
                $this->addReference('member_' . self::$memberIndex, $member);
            }
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SplitterFixtures::class
        ];
    }
}
