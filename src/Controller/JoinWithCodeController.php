<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\Role;
use App\Enum\ShareCodeType;
use App\Form\JoinWithCodeFormType;
use App\Interface\ShareableEntityInterface;
use App\Service\ShareCodeManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Role::USER->value)]
#[Route(
    '/join-with-code',
    name: 'app_join_with_code',
    methods: ['GET', 'POST']
)]
class JoinWithCodeController extends AbstractController
{
    public function __invoke(
        Request $request,
        ShareCodeManager $shareCodeManager,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var ?User $user */
        $user = $this->getUser();

        $form = $this->createForm(JoinWithCodeFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processJoinRequest($form, $shareCodeManager, $entityManager, $user);
        }

        return $this->render('share_code/join.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Traite la demande de rejoindre un groupe avec un code
     */
    private function processJoinRequest(
        $form,
        ShareCodeManager $shareCodeManager,
        EntityManagerInterface $entityManager,
        User $user
    ): Response {
        $data = $form->getData();
        $code = $data['code'];

        // Validation préalable du format avec l'Enum
        if (!$shareCodeManager->validateCodeFormat($code, ShareCodeType::CODE)) {
            return $this->redirectWithError('❌ Le code saisi n\'a pas le bon format.');
        }

        // Utiliser le code et récupérer l'entité
        $entity = $shareCodeManager->useShareCode($code, $user->getUserIdentifier());

        if (!$entity) {
            return $this->redirectWithError('❌ Code invalide, expiré ou déjà utilisé.');
        }

        if (!$entity instanceof ShareableEntityInterface) {
            return $this->redirectWithError('❌ Type d\'entité non supporté pour le partage.');
        }

        if (!$entity->canBeAccessedBy($user)) {
            return $this->redirectWithError('❌ Vous n\'avez pas les permissions pour accéder à cette ressource.');
        }

        return $this->grantAccessAndRedirect($entity, $entityManager, $user);
    }

    /**
     * Accorde l'accès à l'utilisateur et redirige vers l'entité
     */
    private function grantAccessAndRedirect(
        ShareableEntityInterface $entity,
        EntityManagerInterface $entityManager,
        User $user
    ): Response {
        $entity->grantAccessTo($user);
        $entityManager->flush();

        $this->addFlash('success', '🎉 Vous avez rejoint avec succès : ' . $entity->getDisplayName());

        return $this->redirectToEntity($entity);
    }

    /**
     * Ajoute un message d'erreur et redirige vers le formulaire
     */
    private function redirectWithError(string $message): Response
    {
        $this->addFlash('danger', $message);
        return $this->redirectToRoute('app_join_with_code');
    }

    /**
     * Redirige vers la page appropriée selon le type d'entité
     */
    private function redirectToEntity(object $entity): Response
    {
        return match (get_class($entity)) {
            'App\Entity\Splitter' => $this->redirectToRoute('app_splitter_show', ['id' => $entity->getId()]),
            default => $this->redirectToRoute('app_home'),
        };
    }
}
