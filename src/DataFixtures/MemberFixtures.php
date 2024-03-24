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

        for ($i = 1; $i <= UserFixtures::$userIndex; $i++) {
            for ($j = 1; $j <= 2; $j++) {
                self::$memberIndex++;
                $member = new Member();
                $member->setNickname($faker->firstName() . '_' . $i . $j);
                $member->setUser($this->getReference('user_' . $i));
                $member->setEditor($faker->boolean());
                $manager->persist($member);
                $this->addReference('member_' . $i . $j, $member);
            }
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
