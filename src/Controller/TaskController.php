<?php

// src/Controller/TaskController.php
namespace App\Controller;

use App\Entity\Tag;
use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/task')]
class TaskController extends AbstractController
{
    #[Route('/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
// dummy code - add some example tags to the task
        // (otherwise, the template will render an empty list of tags)
//        $tag1 = new Tag();
//        $tag1->setName('tag1');
//        $task->addTag($tag1);
// end dummy code

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
        // ... do your form processing, like saving the Task and Tag entities
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('app_member_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/newUX.html.twig', [
            'form' => $form,
            'task' => $task,
        ]);
    }

    #[Route(
        '/{id}/edit',
        name: 'app_task_edit',
        methods: ['GET', 'POST']
    )]
    public function edit(
        Task $task,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $originalTags = new ArrayCollection();

        // Create an ArrayCollection of the current Tag objects in the database
        foreach ($task->getTags() as $tag) {
            $originalTags->add($tag);
        }

        $editForm = $this->createForm(TaskType::class, $task);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // remove the relationship between the tag and the Task

            foreach ($originalTags as $tag) {
                if (false === $task->getTags()->contains($tag)) {
                    // remove the Task from the Tag
                    //$tag->getTask()->removeElement($task);

                    // if it was a many-to-one relationship, remove the relationship like this
                    // $tag->setTask(null);

                    //$entityManager->persist($tag);

                    // if you wanted to delete the Tag entirely, you can also do that
                    $entityManager->remove($tag);
                }
            }

            $entityManager->persist($task);
            $entityManager->flush();

            // redirect back to some edit page
            return $this->redirectToRoute('app_member_index', [], Response::HTTP_SEE_OTHER);
        }

        // ... render some form template
        return $this->render('task/new.html.twig', [
            'form' => $editForm,
        ]);
    }
}
