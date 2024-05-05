<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\Splitter;
use App\Entity\User;
use App\Form\JoinSplitterType;
use App\Form\ShareSplitterType;
use App\Form\SplitterType;
use App\Repository\SplitterRepository;
use App\Service\BalanceCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Internal\TopologicalSort\CycleDetectedException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/splitter')]
class SplitterController extends AbstractController
{
    #[Route('/new', name: 'app_splitter_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        SplitterRepository $splitterRepository
    ): Response {

        $splitter = new Splitter();
        $member = new Member();
        $splitter->addMember($member);

        $form = $this->createForm(SplitterType::class, $splitter);
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
                ['id' => $splitter->getId()],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('splitter/new.html.twig', [
            'form' => $form,
            'splitter' => $splitter
        ]);
    }

    #[Route(
        '/{id}',
        name: 'app_splitter_show',
        requirements: [
            'id' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$'
        ],
        methods: ['GET']
    )]
    public function show(
        Splitter $splitter,
        BalanceCalculator $balanceCalculator
    ): Response {

        $balancePerId = $balanceCalculator->calculateIndividualBalance($splitter);
        $transfers = $balanceCalculator->calculateTransfer($balancePerId);

        return $this->render('splitter/show.html.twig', [
            'splitter' => $splitter,
            'balancePerId' => $balancePerId,
            'transfers' => $transfers,
        ]);
    }

    #[Route(
        '/{id}/edit',
        name: 'app_splitter_edit',
        requirements: [
            'page' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$'
        ],
        methods: ['GET', 'POST']
    )]
    public function edit(
        Request $request,
        Splitter $splitter,
        SplitterRepository $splitterRepository
    ): Response {

        /* @var ?User $user */
        $user = $this->getUser();
        //dd($splitter->getOwner(), $user->getAppUser());

        if ($splitter->getOwner() !== $user->getAppUser() && !$this->isGranted('ROLE_ADMIN')) {
//            $this->addFlash('danger', '🤨 Vous ne pouvez pas éditer un Splitter qui ne vous appartient pas !');
//            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
            throw new AccessDeniedException('Vous ne pouvez pas éditer un Splitter qui ne vous appartient pas !');
        }

        $form = $this->createForm(SplitterType::class, $splitter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $splitterRepository->save($splitter, true);

            $this->addFlash('success', '🙂 Votre Splitter a bien été édité !');

            return $this->redirectToRoute(
                'app_splitter_show',
                ['id' => $splitter->getId()],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('splitter/edit.html.twig', [
            'form' => $form,
            'splitter' => $splitter
        ]);
    }

    #[Route(
        '/{id}/share',
        name: 'app_splitter_share',
        requirements: [
            'page' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$'
        ],
        methods: ['GET', 'POST']
    )]
    public function share(
        Request $request,
        Splitter $splitter,
        MailerInterface $mailer
    ): Response {

        /**
         * @var ?User $user
         */
        $user = $this->getUser();

        $form = $this->createForm(ShareSplitterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $email = (new TemplatedEmail())
                ->from(new Address('no-reply@splitter.fr', 'Splitter Bot'))
                ->to($data['email'])
                ->subject('On vous invite dans un Splitter !')
                ->htmlTemplate('splitter/shareEmail.html.twig')
                ->context([
                    'splitter' => $splitter,
                    'user' => $user,
                ]);
            $mailer->send($email);

            $this->addFlash('success', '🙂 Votre ami a bien été prévenu par mail !');

            return $this->redirectToRoute(
                'app_splitter_show',
                ['id' => $splitter->getId()],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('splitter/share.html.twig', [
            'splitter' => $splitter,
            'form' => $form,
        ]);
    }

    #[Route(
        '/{id}/join',
        name: 'app_splitter_join',
        requirements: [
            'page' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$'
        ],
        methods: ['GET']
    )]
    public function join(
        Splitter $splitter,
        SplitterRepository $splitterRepository
    ): Response {

        /**
         * @var ?User $user
         */
        $user = $this->getUser();
        $user->getAppUser()->addFavoriteSplitter($splitter);
        $splitterRepository->save($splitter, true);

        $this->addFlash('success', '🙂 Vous avez bien rejoint le Splitter !');

        return $this->redirectToRoute(
            'app_splitter_show',
            ['id' => $splitter->getId()],
            Response::HTTP_SEE_OTHER
        );
    }

    // supprimer un splitter des favoris
    #[Route(
        '/{id}/leave',
        name: 'app_splitter_leave',
        requirements: [
            'page' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$'
        ],
        methods: ['GET']
    )]
    public function leave(
        Splitter $splitter,
        SplitterRepository $splitterRepository
    ): Response {

        /**
         * @var ?User $user
         */
        $user = $this->getUser();
        $user->getAppUser()->removeFavoriteSplitter($splitter);
        $splitterRepository->save($splitter, true);

        $this->addFlash('success', '🙂 Vous avez bien quitté le Splitter !');

        return $this->redirectToRoute(
            'app_splitter_show',
            ['id' => $splitter->getId()],
            Response::HTTP_SEE_OTHER
        );
    }

    #[Route('/joinbycode', name: 'app_splitter_join_by_code', methods: ['GET', 'POST'])]
    public function joinByCode(
        Request $request,
        SplitterRepository $splitterRepository
    ): Response {

        /**
         * @var ?User $user
         */
        $user = $this->getUser();

        $form = $this->createForm(JoinSplitterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $splitter = $splitterRepository->findOneBy([
                'id' => $data['code']
            ]);

            $splitter->addFavoritedByUser($user->getAppUser());
            $splitterRepository->save($splitter, true);

            $this->addFlash('success', '🙂 Vous avez bien rejoint le Splitter !');

            return $this->redirectToRoute(
                'app_splitter_show',
                ['id' => $splitter->getId()],
                Response::HTTP_SEE_OTHER
            );
        }
        return $this->render('splitter/join.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_splitter_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Splitter $splitter,
        EntityManagerInterface $entityManager
    ): Response {

        /* @var ?User $user */
        $user = $this->getUser();
        if ($splitter->getOwner() !== $user->getAppUser() && !$this->isGranted('ROLE_ADMIN')) {
//            $this->addFlash('danger', '🤨 Vous ne pouvez pas éditer un Splitter qui ne vous appartient pas !');
//            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
            throw new AccessDeniedException('Vous ne pouvez pas éditer un Splitter qui ne vous appartient pas !');
        }

        try {
            if ($this->isCsrfTokenValid('delete' . $splitter->getId(), $request->request->get('_token'))) {
//                $splitter->unsetOwner();
//                $entityManager->persist($splitter);
//                $entityManager->flush();
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
