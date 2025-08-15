<?php

namespace App\DataFixtures;

use App\Entity\AppUser;
use App\Entity\Expense;
use App\Entity\ExpenseCategory;
use App\Entity\Member;
use App\Entity\Splitter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class ExpenseFixtures extends Fixture implements DependentFixtureInterface
{
    public static int $expenseIndex = 0;

    // ajouter un constructor
//    public function __construct()
//    {
//        self::$expenseIndex = 0;
//    }

    public function createExpense1(Generator $faker, ObjectManager $manager): void
    {
        self::$expenseIndex++;
        $expense = new Expense();
        $expense->setName('Dépense N°' . self::$expenseIndex);
        $expense->setCategory(
            $this->getReference(
                'expenseCategory_' . $faker->numberBetween(
                    1,
                    ExpenseCategoryFixtures::$expenseCategoryIndex
                ),
                ExpenseCategory::class
            )
        );
        $expense->setCreatedAt($faker->dateTime);
        $expense->setMadeAt($faker->dateTime);
        $expense->setAmount($faker->randomFloat(2, 10, 100));
        $expense->setDevise('€');
        $expense->setPaidBy($this->getReference('member_11', Member::class));
        $expense->setAddedBy($this->getReference('appUser_1', AppUser::class));
        $expense->setSplitter($this->getReference('splitter_1', Splitter::class));
        $expense->addBeneficiary($this->getReference('member_11', Member::class));
        $expense->addBeneficiary($this->getReference('member_21', Member::class));
        $expense->addBeneficiary($this->getReference('member_31', Member::class));
        $manager->persist($expense);
    }

    public function createExpense2(Generator $faker, ObjectManager $manager): void
    {
        self::$expenseIndex++;
        $expense = new Expense();
        $expense->setName('Dépense N°' . self::$expenseIndex);
        $expense->setCategory(
            $this->getReference(
                'expenseCategory_' . $faker->numberBetween(
                    1,
                    ExpenseCategoryFixtures::$expenseCategoryIndex
                ),
                ExpenseCategory::class
            )
        );
        $expense->setCreatedAt($faker->dateTime);
        $expense->setMadeAt($faker->dateTime);
        $expense->setAmount($faker->randomFloat(2, 10, 100));
        $expense->setDevise('€');
        $expense->setPaidBy($this->getReference('member_21', Member::class));
        $expense->setAddedBy($this->getReference('appUser_2', AppUser::class));
        $expense->setSplitter($this->getReference('splitter_1', Splitter::class));
        $expense->addBeneficiary($this->getReference('member_11', Member::class));
        $expense->addBeneficiary($this->getReference('member_21', Member::class));
        $expense->addBeneficiary($this->getReference('member_41', Member::class));
        $manager->persist($expense);
    }

    public function createExpense3(Generator $faker, ObjectManager $manager): void
    {
        self::$expenseIndex++;
        $expense = new Expense();
        $expense->setName('Dépense N°' . self::$expenseIndex);
        $expense->setCategory(
            $this->getReference(
                'expenseCategory_' . $faker->numberBetween(
                    1,
                    ExpenseCategoryFixtures::$expenseCategoryIndex
                ),
                ExpenseCategory::class
            )
        );
        $expense->setCreatedAt($faker->dateTime);
        $expense->setMadeAt($faker->dateTime);
        $expense->setAmount($faker->randomFloat(2, 10, 100));
        $expense->setDevise('€');
        $expense->setPaidBy($this->getReference('member_12', Member::class));
        $expense->setAddedBy($this->getReference('appUser_1', AppUser::class));
        $expense->setSplitter($this->getReference('splitter_2', Splitter::class));
        $expense->addBeneficiary($this->getReference('member_12', Member::class));
        $expense->addBeneficiary($this->getReference('member_51', Member::class));
        $expense->addBeneficiary($this->getReference('member_61', Member::class));
        $manager->persist($expense);
    }

    public function createExpense4(Generator $faker, ObjectManager $manager): void
    {
        self::$expenseIndex++;
        $expense = new Expense();
        $expense->setName('Dépense N°' . self::$expenseIndex);
        $expense->setCategory(
            $this->getReference(
                'expenseCategory_' . $faker->numberBetween(
                    1,
                    ExpenseCategoryFixtures::$expenseCategoryIndex
                ),
                ExpenseCategory::class
            )
        );
        $expense->setCreatedAt($faker->dateTime);
        $expense->setMadeAt($faker->dateTime);
        $expense->setAmount($faker->randomFloat(2, 10, 100));
        $expense->setDevise('€');
        $expense->setPaidBy($this->getReference('member_22', Member::class));
        $expense->setAddedBy($this->getReference('appUser_2', AppUser::class));
        $expense->setSplitter($this->getReference('splitter_2', Splitter::class));
        $expense->addBeneficiary($this->getReference('member_22', Member::class));
        $expense->addBeneficiary($this->getReference('member_12', Member::class));
        $expense->addBeneficiary($this->getReference('member_61', Member::class));
        $manager->persist($expense);
    }

    public function createExpense5(Generator $faker, ObjectManager $manager): void
    {
        self::$expenseIndex++;
        $expense = new Expense();
        $expense->setName('Dépense N°' . self::$expenseIndex);
        $expense->setCategory(
            $this->getReference(
                'expenseCategory_' . $faker->numberBetween(
                    1,
                    ExpenseCategoryFixtures::$expenseCategoryIndex
                ),
                ExpenseCategory::class
            )
        );
        $expense->setCreatedAt($faker->dateTime);
        $expense->setMadeAt($faker->dateTime);
        $expense->setAmount($faker->randomFloat(2, 10, 100));
        $expense->setDevise('€');
        $expense->setPaidBy($this->getReference('member_12', Member::class));
        $expense->setAddedBy($this->getReference('appUser_1', AppUser::class));
        $expense->setSplitter($this->getReference('splitter_2', Splitter::class));
        $expense->addBeneficiary($this->getReference('member_12', Member::class));
        $expense->addBeneficiary($this->getReference('member_22', Member::class));
        $expense->addBeneficiary($this->getReference('member_51', Member::class));
        $manager->persist($expense);
    }






    public function createExpense6(Generator $faker, ObjectManager $manager): void
    {
        self::$expenseIndex++;
        $expense = new Expense();
        $expense->setName('Dépense N°' . self::$expenseIndex);
        $expense->setCategory(
            $this->getReference(
                'expenseCategory_' . $faker->numberBetween(
                    1,
                    ExpenseCategoryFixtures::$expenseCategoryIndex
                ),
                ExpenseCategory::class
            )
        );
        $expense->setCreatedAt($faker->dateTime);
        $expense->setMadeAt($faker->dateTime);
        $expense->setAmount($faker->randomFloat(2, 10, 100));
        $expense->setDevise('€');
        $expense->setPaidBy($this->getReference('member_32', Member::class));
        $expense->setAddedBy($this->getReference('appUser_3', AppUser::class));
        $expense->setSplitter($this->getReference('splitter_3', Splitter::class));
        $expense->addBeneficiary($this->getReference('member_32', Member::class));
        $expense->addBeneficiary($this->getReference('member_42', Member::class));
        $expense->addBeneficiary($this->getReference('member_52', Member::class));
        $manager->persist($expense);
    }

    public function createExpense7(Generator $faker, ObjectManager $manager): void
    {
        self::$expenseIndex++;
        $expense = new Expense();
        $expense->setName('Dépense N°' . self::$expenseIndex);
        $expense->setCategory(
            $this->getReference(
                'expenseCategory_' . $faker->numberBetween(
                    1,
                    ExpenseCategoryFixtures::$expenseCategoryIndex
                ),
                ExpenseCategory::class
            )
        );
        $expense->setCreatedAt($faker->dateTime);
        $expense->setMadeAt($faker->dateTime);
        $expense->setAmount($faker->randomFloat(2, 10, 100));
        $expense->setDevise('€');
        $expense->setPaidBy($this->getReference('member_52', Member::class));
        $expense->setAddedBy($this->getReference('appUser_5', AppUser::class));
        $expense->setSplitter($this->getReference('splitter_3', Splitter::class));
        $expense->addBeneficiary($this->getReference('member_52', Member::class));
        $expense->addBeneficiary($this->getReference('member_62', Member::class));
        $expense->addBeneficiary($this->getReference('member_32', Member::class));
        $manager->persist($expense);
    }


    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $this->createExpense1($faker, $manager);
        $this->createExpense2($faker, $manager);
        $this->createExpense3($faker, $manager);
        $this->createExpense4($faker, $manager);
        $this->createExpense5($faker, $manager);
        $this->createExpense6($faker, $manager);
        $this->createExpense7($faker, $manager);

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
