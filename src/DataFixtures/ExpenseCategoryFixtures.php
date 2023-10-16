<?php

namespace App\DataFixtures;

use App\Entity\ExpenseCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ExpenseCategoryFixtures extends Fixture implements DependentFixtureInterface
{
    public static int $expenseCategoryIndex = 0;

    public const CATEGORIES = ['Alimentation', 'Assurance', 'Bar', 'Charges', 'Divertissement', 'Logement', 'Loyer',
        'Parking', 'Restaurant', 'Shopping', 'Santé', 'Transport', 'Autre'];

    public function load(ObjectManager $manager): void
    {
        foreach (self::CATEGORIES as $categoryName) {
            self::$expenseCategoryIndex++;
            $category = new ExpenseCategory();
            $category->setName($categoryName);
            $category->setAddedBy($this->getReference('admin'));
            $category->setType('Catégories Prédéfinies');

            $manager->persist($category);
            $this->addReference('expenseCategory_' . self::$expenseCategoryIndex, $category);
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
