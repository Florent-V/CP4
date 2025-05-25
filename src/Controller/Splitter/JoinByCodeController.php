<?php

namespace App\Controller\Splitter;

use App\Entity\User;
use App\Enum\Role;
use App\Form\JoinSplitterFormType;
use App\Repository\SplitterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Role::USER->value)]
#[Route(
    '/splitter/joinbycode',
    name: 'app_splitter_join_by_code',
    methods: ['GET', 'POST']
)]
class JoinByCodeController extends AbstractController
{
    public function __invoke(
        Request $request,
        SplitterRepository $splitterRepository,
    ): Response {
        /**
         * @var ?User $user
         */
        $user = $this->getUser();
        $appUser = $user->getAppUser();

        $form = $this->createForm(JoinSplitterFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $splitter = $splitterRepository->findOneBy([
                'id' => $data['code']
            ]);

            if ($splitter) {
                $splitter->addFavoritedByUser($appUser);
                $splitterRepository->save($splitter, true);

                $this->addFlash('success', '🙂 Vous avez bien rejoint le Splitter !');

                return $this->redirectToRoute(
                    'app_splitter_show',
                    ['id' => $splitter->getId()]
                );
            }
            $this->addFlash('danger', 'Aucun Splitter trouvé avec ce code.');
        }
        return $this->render(
            'splitter/join.html.twig',
            [
            'form' => $form,
            ]
        );
    }
}
