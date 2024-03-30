<?php

namespace App\Twig\Form;

use App\Entity\Member;
use App\Entity\Splitter;
use App\Form\SplitterType;
use Exception;
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
    public ?Splitter $initialFormData = null;

    #[LiveProp(writable: true)]
    public ?string $label = '';

    /**
     * @throws Exception
     */
    public function getButtonLabel(): string
    {
        if (!$this->label) {
            throw new Exception('Le label est obligatoire');
        }
        return $this->label;
    }

    protected function instantiateForm(): FormInterface
    {
        if (!$this->initialFormData) {
            throw new Exception('Le formulaire doit être initialisé');
        }

        return $this->createForm(
            SplitterType::class,
            $this->initialFormData
        );
    }
}
