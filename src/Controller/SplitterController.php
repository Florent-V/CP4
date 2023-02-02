<?php

namespace App\Controller;

use App\Entity\Splitter;
use App\Entity\User;
use App\Form\JoinSplitterType;
use App\Form\ShareSplitterType;
use App\Form\SplitterType;
use App\Repository\SplitterRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
#[Route('/splitter')]
class SplitterController extends AbstractController
{
    #[Route('/', name: 'app_splitter_index', methods: ['GET'])]
    public function index(
        SplitterRepository $splitterRepository
    ): Response {
        return $this->render('splitter/index.html.twig', [
            'splitters' => $splitterRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_splitter_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        SplitterRepository $splitterRepository
    ): Response {
        $splitter = new Splitter();
        $form = $this->createForm(SplitterType::class, $splitter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $splitter->setOwnedBy($this->getUser());
            $splitter->addMember($this->getUser());
            $splitterRepository->save($splitter, true);

            $this->addFlash('success', '🙂 Votre Splitter a bien été crée !');

            return $this->redirectToRoute(
                'app_splitter_show',
                ['id' => $splitter->getId()],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('splitter/new.html.twig', [
            'splitter' => $splitter,
            'form' => $form,
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
        Splitter $splitter
    ): Response {
        return $this->render('splitter/show.html.twig', [
            'splitter' => $splitter,
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
            'splitter' => $splitter,
            'form' => $form,
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

        $splitter->addMember($user);
        $splitterRepository->save($splitter, true);

        $this->addFlash('success', '🙂 Vous avez bien rejoint le Splitter !');

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

            $splitter->addMember($user);
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
        SplitterRepository $splitterRepository
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $splitter->getId(), $request->request->get('_token'))) {
            $splitterRepository->remove($splitter, true);
        }

        return $this->redirectToRoute('app_splitter_index', [], Response::HTTP_SEE_OTHER);
    }
}
