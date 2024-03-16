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

        for ($i = 1; $i <= SplitterFixtures::$splitExpense; $i++) {
            for ($j = 1; $j <= 4; $j++) {
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
                $expense->setPaidBy($this->getReference('member_' . ($faker->numberBetween(4 * $i - 3, 4 * $i))));
                $expense->setSplitter($this->getReference('splitter_' . $i));
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
