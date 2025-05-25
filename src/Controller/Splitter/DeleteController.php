<?php

namespace App\Controller\Splitter;

use App\Entity\Splitter;
use App\Entity\User;
use App\Enum\Role;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Internal\TopologicalSort\CycleDetectedException;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\SplitterAccessManager;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Role::USER->value)]
#[Route(
    '/splitter/{splitter_id}/delete',
    name: 'app_splitter_delete',
    methods: ['POST']
)]
class DeleteController extends AbstractController
{
    public function __invoke(
        Request $request,
        #[MapEntity(mapping: ['splitter_id' => 'id'])]
        Splitter $splitter,
        EntityManagerInterface $entityManager,
        SplitterAccessManager $accessManager
    ): Response {
        /**
         * @var ?User $user
         */
        $user = $this->getUser();
        $accessManager->checkEditAccess($splitter, $user);

        if ($splitter->getOwner() !== $user->getAppUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Vous ne pouvez pas éditer un Splitter qui ne vous appartient pas !');
        }

        try {
            if ($this->isCsrfTokenValid('delete' . $splitter->getId(), $request->request->get('_token'))) {
                $entityManager->remove($splitter);
                $entityManager->flush();
            }
        } catch (CycleDetectedException $e) {
            $cycle = $e->getCycle();

            foreach ($cycle as $node) {
                dd($node);
            }
        }
        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
}
