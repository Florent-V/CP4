<?php

namespace App\DataFixtures;

use App\Entity\Expense;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ExpenseFixtures extends Fixture implements DependentFixtureInterface
{
    public static int $expenseIndex = 0;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        foreach (SplitterFixtures::SPLITTERS as $key => $splitter) {
            for ($i = 1; $i <= 10; $i++) {
                self::$expenseIndex++;
                $expense = new Expense();
                $expense->setName('Dépense N°' . self::$expenseIndex);
                $expense->setCategory(
                    $this->getReference(
                        'expenseCategory_' . $faker->numberBetween(
                            1,
                            ExpenseCategoryFixtures::$expenseCategoryIndex
                        )
                    )
                );
                $expense->setCreatedAt($faker->dateTime);
                $expense->setMadeAt($faker->dateTime);
                $expense->setAmount($faker->randomFloat(2, 10, 100));
                $expense->setDevise('€');
                $expense->setPaidBy($this->getReference('user_' . ($faker->randomElement($splitter) - 1)));
                $expense->setSplitter($this->getReference('splitter_' . ($key + 1)));
                $manager->persist($expense);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ExpenseCategoryFixtures::class,
            SplitterFixtures::class,
        ];
    }
}
