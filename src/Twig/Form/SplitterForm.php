<?php

namespace App\Twig\Form;

use App\Entity\Member;
use App\Entity\Splitter;
use App\Form\SplitterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent]
class SplitterForm extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    #[LiveProp(fieldName: 'formData')]
    public ?Splitter $splitter = null;

    protected function instantiateForm(): FormInterface
    {
        $this->splitter = new Splitter();
        $member = new Member();
        $this->splitter->addMember($member);
        return $this->createForm(
            SplitterType::class,
            $this->splitter
        );
    }
}
