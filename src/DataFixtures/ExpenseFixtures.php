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
        $expense->setPaidBy($this->getReference('member_11'));
        $expense->setAddedBy($this->getReference('member_11'));
        $expense->setSplitter($this->getReference('splitter_1'));
        $expense->addBeneficiary($this->getReference('member_11'));
        $expense->addBeneficiary($this->getReference('member_21'));
        $expense->addBeneficiary($this->getReference('member_31'));
        $manager->persist($expense);

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
        $expense->setPaidBy($this->getReference('member_21'));
        $expense->setAddedBy($this->getReference('member_21'));
        $expense->setSplitter($this->getReference('splitter_1'));
        $expense->addBeneficiary($this->getReference('member_11'));
        $expense->addBeneficiary($this->getReference('member_21'));
        $expense->addBeneficiary($this->getReference('member_41'));
        $manager->persist($expense);

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
        $expense->setPaidBy($this->getReference('member_12'));
        $expense->setAddedBy($this->getReference('member_12'));
        $expense->setSplitter($this->getReference('splitter_2'));
        $expense->addBeneficiary($this->getReference('member_12'));
        $expense->addBeneficiary($this->getReference('member_51'));
        $expense->addBeneficiary($this->getReference('member_61'));
        $manager->persist($expense);

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
        $expense->setPaidBy($this->getReference('member_22'));
        $expense->setAddedBy($this->getReference('member_22'));
        $expense->setSplitter($this->getReference('splitter_2'));
        $expense->addBeneficiary($this->getReference('member_22'));
        $expense->addBeneficiary($this->getReference('member_12'));
        $expense->addBeneficiary($this->getReference('member_61'));
        $manager->persist($expense);

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
        $expense->setPaidBy($this->getReference('member_12'));
        $expense->setAddedBy($this->getReference('member_12'));
        $expense->setSplitter($this->getReference('splitter_2'));
        $expense->addBeneficiary($this->getReference('member_12'));
        $expense->addBeneficiary($this->getReference('member_22'));
        $expense->addBeneficiary($this->getReference('member_51'));
        $manager->persist($expense);

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
        $expense->setPaidBy($this->getReference('member_32'));
        $expense->setAddedBy($this->getReference('member_32'));
        $expense->setSplitter($this->getReference('splitter_3'));
        $expense->addBeneficiary($this->getReference('member_32'));
        $expense->addBeneficiary($this->getReference('member_42'));
        $expense->addBeneficiary($this->getReference('member_52'));
        $manager->persist($expense);

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
        $expense->setPaidBy($this->getReference('member_52'));
        $expense->setAddedBy($this->getReference('member_52'));
        $expense->setSplitter($this->getReference('splitter_3'));
        $expense->addBeneficiary($this->getReference('member_52'));
        $expense->addBeneficiary($this->getReference('member_62'));
        $expense->addBeneficiary($this->getReference('member_32'));
        $manager->persist($expense);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            MemberFixtures::class,
            ExpenseCategoryFixtures::class,
            SplitterFixtures::class,
        ];
    }
}
