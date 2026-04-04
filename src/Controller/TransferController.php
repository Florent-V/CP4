<?php

namespace App\Controller;

use App\Entity\Splitter;
use App\Entity\Transfer;
use App\Entity\User;
use App\Enum\Role;
use App\Form\TransferFormType;
use App\Repository\TransferRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Role::USER->value)]
#[Route('/splitter/{splitter_id}/transfer', name: 'app_transfer_')]
class TransferController extends AbstractController
{
    /**
     * Vérifie si l'utilisateur actuel a accès à la ressource splitter.
     *
     * @param Splitter $splitter
     * @throws AccessDeniedException Si l'utilisateur n'a pas accès
     */
    private function rejectIfNotMember(Splitter $splitter): ?User
    {
        /**
         * @var ?User $user
         */
        $user = $this->getUser();
        if (!$user->getAppUser()->getFavoriteSplitters()->contains($splitter) && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Accès non autorisé à cette ressource.');
        }
        return $user;
    }

    /**
     * Vérifie si l'utilisateur actuel a accès à la ressource splitter.
     *
     * @param Splitter $splitter
     * @param Transfer $transfer
     */
    private function rejectIfNotAdmin(Splitter $splitter, Transfer $transfer): void
    {
        /**
         * @var ?User $user
         */
        $user = $this->getUser();
        if (
            $splitter->getOwner() !== $user->getAppUser()
            && $transfer->getAddedBy() !== $user->getAppUser()
            && !$this->isGranted('ROLE_ADMIN')
        ) {
            throw new AccessDeniedException('Accès non autorisé à cette ressource.');
        }
    }

    #[Route(
        '/new',
        name: 'new',
        requirements: [
            'splitter_id' => Requirement::UUID
        ],
        methods: ['GET', 'POST']
    )]
    public function new(
        Request $request,
        #[MapEntity(mapping: ['splitter_id' => 'id'])]
        Splitter $splitter,
        TransferRepository $transferRepository
    ): Response {

        $user = $this->rejectIfNotMember($splitter);
        $transfer = new Transfer();
        // Le splitter est fixé côté serveur pour éviter toute manipulation
        $transfer->setSplitter($splitter);
        $form = $this->createForm(TransferFormType::class, $transfer, [
            'splitter' => $splitter
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Validate that from and to members are different
            if ($transfer->getFromMember() === $transfer->getToMember()) {
                $this->addFlash('error', 'Les deux membres doivent être différents.');
                return $this->render('transfer/new.html.twig', [
                    'transfer' => $transfer,
                    'form' => $form,
                    'splitter' => $splitter,
                ]);
            }

            $transfer->setAddedBy($user->getAppUser());
            // Sécurité : on re-force le splitter pour éviter toute manipulation du formulaire
            $transfer->setSplitter($splitter);
            $transferRepository->save($transfer, true);

            $this->addFlash('success', 'Le transfert a été enregistré avec succès.');
            return $this->redirectToRoute('app_splitter_show', ['id' => $splitter->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transfer/new.html.twig', [
            'transfer' => $transfer,
            'form' => $form,
            'splitter' => $splitter,
        ]);
    }

    #[Route('/{transfer_id}', name: 'delete', requirements: [
        'splitter_id' => Requirement::UUID,
        'transfer_id' => '\d+'
    ], methods: ['POST'])]
    public function delete(
        Request $request,
        #[MapEntity(mapping: ['splitter_id' => 'id'])]
        Splitter $splitter,
        #[MapEntity(mapping: ['transfer_id' => 'id'])]
        Transfer $transfer,
        TransferRepository $transferRepository
    ): Response {

        $this->rejectIfNotAdmin($splitter, $transfer);

        if ($this->isCsrfTokenValid('delete' . $transfer->getId(), $request->request->get('_token'))) {
            $transferRepository->remove($transfer, true);
            $this->addFlash('success', 'Le transfert a été supprimé.');
        }

        return $this->redirectToRoute(
            'app_splitter_show',
            [
                'id' => $splitter->getId(),
            ],
            Response::HTTP_SEE_OTHER
        );
    }
}
