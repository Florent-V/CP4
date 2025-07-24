<?php

namespace App\Controller\Share;

use App\Entity\User;
use App\Enum\Role;
use App\Enum\ShareCodeType;
use App\Form\ShareEmailFormType;
use App\Service\ShareCodeManager;
use App\Service\ShareEntityValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur pour l'envoi d'emails de partage via page dédiée
 */
#[IsGranted(Role::USER->value)]
#[Route(
    '/share/email/{entityType}/{entityId}',
    name: 'share_email_form',
    methods: ['GET', 'POST']
)]
class ShareEmailController extends AbstractController
{
    public function __invoke(
        string $entityType,
        string $entityId,
        Request $request,
        ShareEntityValidator $validator,
        ShareCodeManager $shareCodeManager,
        MailerInterface $mailer
    ): Response {
        // Valider l'entité et les permissions
        $entity = $validator->getAndValidateEntity($entityType, $entityId);

        // Créer le formulaire
        $form = $this->createForm(ShareEmailFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                // Générer un lien sécurisé pour l'email
                $shareCode = $shareCodeManager->generateShareCode($entity, true, ShareCodeType::LINK);
                $shareUrl = $this->generateUrl(
                    'app_join_by_secure_link',
                    [
                        'token' => $shareCode->getCode()
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
                /**
                 * @var ?User $user
                 */
                $user = $this->getUser();

                // Créer et envoyer l'email
                $email = (new Email())
                    ->from($this->getParameter('app.mail_from'))
                    ->to($data['email'])
                    ->subject('🎯 ' .
                        $user->getFirstName() .
                        ' ' .
                        $user->getLastName() .
                        ' vous invite à rejoindre "' .
                        $entity->getDisplayName() . '"')
                    ->html($this->renderView('emails/share_invitation.html.twig', [
                        'entity' => $entity,
                        'shareUrl' => $shareUrl,
                        'senderName' => $user->getFirstName() . ' ' . $user->getLastName(),
                        'entityType' => $entityType
                    ]));

                $mailer->send($email);

                $this->addFlash('success', ' Email envoyé avec succès à ' . $data['email']);

                // Rediriger vers la page de l'entité
                return $this->redirectToRoute('app_splitter_show', ['id' => $entityId]);
            } catch (\Exception $e) {
                $this->addFlash(
                    'error',
                    'Erreur lors de l\'envoi de l\'email : ' . $e->getMessage()
                );
            }
        }

        return $this->render('share/email_form.html.twig', [
            'form' => $form->createView(),
            'entity' => $entity,
            'entityType' => $entityType,
            'entityId' => $entityId
        ]);
    }
}
