<?php

namespace App\DataFixtures;

use App\Entity\SplitterCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SplitterCategoryFixtures extends Fixture
{
    public static int $splitCategoryIndex = 0;
    public const CATEGORIES = [
        'Voyage' => 'noto:airplane-departure',
        'Colocation' => 'fluent-color:building-home-32',
        'Couple' => 'openmoji:couple-with-heart',
        'Vie Quotidienne' => 'fluent-emoji:house-with-garden',
        'Évènement' => 'noto-v1:calendar',
        'Projet' => 'twemoji:clipboard',
        'Autre' => 'twemoji:euro-banknote',
        'Cadeau' => 'fxemoji:present',
    ];


    public function load(ObjectManager $manager): void
    {
        foreach (self::CATEGORIES as $name => $icon) {
            self::$splitCategoryIndex++;
            $splitterCategory = new SplitterCategory();
            $splitterCategory->setName($name);
            $splitterCategory->setIcon($icon);
            $manager->persist($splitterCategory);
            $this->addReference('splitterCategory_' . self::$splitCategoryIndex, $splitterCategory);
        }

        $manager->flush();
    }
}
