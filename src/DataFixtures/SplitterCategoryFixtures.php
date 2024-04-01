<?php

namespace App\DataFixtures;

use App\Entity\SplitterCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SplitterCategoryFixtures extends Fixture
{
    public static int $splitCategoryIndex = 0;
    public const CATEGORIES = ['Voyage', 'Colocation', 'Couple', 'Évènement', 'Projet', 'Autre', 'Cadeau'];

    public function load(ObjectManager $manager): void
    {
        foreach (self::CATEGORIES as $categoryName) {
            self::$splitCategoryIndex++;
            $category = new SplitterCategory();
            $category->setName($categoryName);
            $manager->persist($category);

            $this->addReference('splitterCategory_' . self::$splitCategoryIndex, $category);
        }
        $manager->flush();
    }
}
