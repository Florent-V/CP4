<?php

namespace App\Controller\Splitter;

use App\Entity\Splitter;
use App\Entity\Member;
use App\Entity\User;
use App\Enum\Role;
use App\Form\SplitterFormType;
use App\Repository\SplitterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Role::USER->value)]
#[Route(
    '/splitter/new',
    name: 'app_splitter_new',
    methods: ['GET', 'POST']
)]
class NewController extends AbstractController
{
    public function __invoke(
        Request $request,
        SplitterRepository $splitterRepository
    ): Response {

        $splitter = new Splitter();
        $member = new Member();
        $splitter->addMember($member);

        $form = $this->createForm(SplitterFormType::class, $splitter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var ?User $user
             */
            $user = $this->getUser();
            $splitter->setUniqueId(md5(uniqid(strval(time()), true)));
            $splitter->setOwner($user->getAppUser());
            $splitter->addFavoritedByUser($user->getAppUser());
            $splitterRepository->save($splitter, true);

            $this->addFlash('success', '🙂 Votre Splitter a bien été crée !');

            return $this->redirectToRoute(
                'app_splitter_show',
                [
                    'id' => $splitter->getId()
                ],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('splitter/new.html.twig', [
            'form' => $form,
            'splitter' => $splitter
        ]);
    }
}
