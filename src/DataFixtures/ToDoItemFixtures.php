<?php

namespace App\DataFixtures;

use App\Entity\ToDoItem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ToDoItemFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Créez une tâche
        $todoItem1 = new ToDoItem();
        $todoItem1->setName('Tâche 1');
        $todoItem1->setTodoList($this->getReference('todolist1'));

        // Créez une autre tâche
        $todoItem2 = new ToDoItem();
        $todoItem2->setName('Tâche 2');
        $todoItem2->setTodoList($this->getReference('todolist1'));

        $todoItem3 = new ToDoItem();
        $todoItem3->setName('Tâche 3');
        $todoItem3->setTodoList($this->getReference('todolist1'));

        $todoItem4 = new ToDoItem();
        $todoItem4->setName('Tâche 4');
        $todoItem4->setTodoList($this->getReference('todolist2'));

        $todoItem5 = new ToDoItem();
        $todoItem5->setName('Tâche 5');
        $todoItem5->setTodoList($this->getReference('todolist2'));

        $todoItem6 = new ToDoItem();
        $todoItem6->setName('Tâche 6');
        $todoItem6->setTodoList($this->getReference('todolist2'));

        // Enregistrez les tâches
        $manager->persist($todoItem1);
        $manager->persist($todoItem2);
        $manager->persist($todoItem3);
        $manager->persist($todoItem4);
        $manager->persist($todoItem5);
        $manager->persist($todoItem6);

        // Exécutez les requêtes
        $manager->flush();

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ToDoListFixtures::class
        ];
    }
}
