<?php

namespace App\Component;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent('task_form')]
class TaskForm extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    #[LiveProp(fieldName: 'formValues')]
    public ?Task $task = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(
            TaskType::class,
            $this->task
        );
    }

    #[LiveAction]
    public function addTag()
    {
        // "formValues" represents the current data in the form
        // this modifies the form to add an extra comment
        // the result: another embedded comment form!
        // change "comments" to the name of the field that uses CollectionType
        var_dump($this->task);
        var_dump($this->formValues);
        $this->task['tags'][] = [];
    }

    #[LiveAction]
    public function removeTag(#[LiveArg] int $index)
    {
        unset($this->task['tags'][$index]);
    }
}
