<?php

namespace App\Twig\Form;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class RegistrationForm extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public bool $isSuccessful = false;

    #[LiveProp]
    public bool $isSubmitted = false;

    #[LiveProp]
    public ?User $user = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(RegistrationFormType::class, $this->user);
    }

    public function hasValidationErrors(): bool
    {
        return $this->getForm()->isSubmitted() && !$this->getForm()->isValid();
    }

    #[LiveAction]
    public function save(
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        EmailVerifier $emailVerifier
    ): Response {
        $this->submitForm();
        //dd($this->getForm()->getData());
//        dd($this->getForm()->get('plainPassword')->getData());

        /** @var User $user */
        $user = $this->getForm()->getData();
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $this->getForm()->get('plainPassword')->getData()
            )
        );
        $this->resetForm();
        $entityManager->persist($user);
        $entityManager->flush();

        $this->isSuccessful = true;

        // generate a signed url and email it to the user
        $emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address('no-reply@splitter.fr', 'Splitter Bot'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
        // do anything else you need here, like send an email
        $this->addFlash('success', 'Votre compte a bien été créé ! Un mail vous a été envoyé
            pour valider votre compte et confirmer l\'adresse mail');

        return $this->redirectToRoute('app_login');
    }
}
