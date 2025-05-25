<?php

namespace App\Controller\Splitter;

use App\Entity\Splitter;
use App\Entity\User;
use App\Enum\Role;
use App\Form\ShareSplitterFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\SplitterAccessManager;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Role::USER->value)]
#[Route(
    '/splitter/{id}/share',
    name: 'app_splitter_share',
    requirements: [
        'id' => '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-6][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}'
    ],
    methods: ['GET', 'POST']
)]
class ShareController extends AbstractController
{
    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(
        Request $request,
        Splitter $splitter,
        MailerInterface $mailer,
        SplitterAccessManager $accessManager
    ): Response {
        /**
         * @var ?User $user
         */
        $user = $this->getUser();
        $accessManager->checkReadAccess($splitter, $user);

        $form = $this->createForm(ShareSplitterFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $email = (new TemplatedEmail())
                ->from(new Address('florent@f5t.fr', 'Kopeck Bot'))
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
                [
                    'id' => $splitter->getId()
                ],
                Response::HTTP_SEE_OTHER
            );
        }
        return $this->render('splitter/share.html.twig', [
            'splitter' => $splitter,
            'form' => $form,
        ]);
    }
}
