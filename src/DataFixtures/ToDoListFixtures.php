<?php

namespace App\DataFixtures;

use App\Entity\ToDoList;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ToDoListFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $todolist1 = new ToDoList();
        $todolist1->setTitle('Ma première liste');
        $todolist1->setDescription('Ma première liste de tâches');
        $manager->persist($todolist1);
        $this->addReference('todolist1', $todolist1);

        $todolist2 = new ToDoList();
        $todolist2->setTitle('Ma deuxième liste');
        $todolist2->setDescription('Ma deuxième liste de tâches');
        $manager->persist($todolist2);
        $this->addReference('todolist2', $todolist2);

        $manager->flush();
    }
}
