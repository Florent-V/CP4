<?php

namespace App\DataFixtures;

use App\Entity\ExpenseCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ExpenseCategoryFixtures extends Fixture implements DependentFixtureInterface
{
    public static int $expenseCategoryIndex = 0;
    public const CATEGORIES = [
        'Alimentation' => 'marketeq:cart-alt-1',
        'Cadeau' => 'emojione:shopping-bags',
        'Assurance' => 'streamline:insurance-hand-solid',
        'Bar' => 'emojione-v1:clinking-beer-mugs',
        'Charges' => 'marketeq:bill-dollar',
        'Divertissement' => 'icon-park:entertainment',
        'Logement' => 'fluent-emoji:house',
        'Loyer' => 'marketeq:bill-dollar',
        'Parking' => 'icon-park:car',
        'Restaurant' => 'fluent-emoji:fork-and-knife-with-plate',
        'Shopping' => 'noto-v1:shopping-bags',
        'Santé' => 'noto:medical-symbol',
        'Metro' => 'noto:metro',
        'Train' => 'emojione:high-speed-train',
        'Autre' => 'marketeq:wallet-money',
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::CATEGORIES as $categoryName => $icon) {
            self::$expenseCategoryIndex++;
            $category = new ExpenseCategory();
            $category->setName($categoryName);
            $category->setIcon($icon);
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
