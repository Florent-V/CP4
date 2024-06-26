<?php

namespace App\DataFixtures;

use App\Entity\AppUser;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public static int $userIndex = 0;

    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $admin = new User();
        $appUser = new AppUser();
        $admin->setPseudo('FloV5');
        $admin->setFirstName('Florent');
        $admin->setLastName('Vasseur');
        $admin->setEmail('florent@mail.fr');
        $admin->setPhone($faker->phoneNumber());
        $admin->setIsVerified(true);
        $admin->setRoles((array)'ROLE_ADMIN');
        $admin->setAppUser($appUser);

        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            'password'
        );
        $admin->setPassword($hashedPassword);

        $manager->persist($admin);
        $this->addReference('admin', $admin);

        for ($i = 1; $i <= 6; $i++) {
            self::$userIndex++;
            $user = new User();
            $appUser = new AppUser();

            $firstName = $faker->firstName();
            $user->setPseudo($firstName . $faker->year());
            $user->setFirstName($firstName);
            $user->setLastName($faker->lastName());
            $user->setEmail('user' . (self::$userIndex) . '@mail.fr');
            $user->setPhone($faker->phoneNumber());
            $user->setIsVerified(true);
            $user->setRoles((array)'ROLE_USER');
            $user->setAppUser($appUser);

            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                'motdepasse'
            );
            $user->setPassword($hashedPassword);

            $manager->persist($user);
            $manager->persist($appUser);
            $this->addReference('appUser_' . $i, $appUser);
        }
        $manager->flush();
    }
}
