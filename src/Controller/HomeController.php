<?php

namespace App\Controller;

use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
        GroupRepository $groupRepository,
        UserRepository $userRepository
    ): Response
    {

        $groups = $groupRepository->findAll();

        $user = $userRepository->findByNumberGroup();
        //dd($user);

        return $this->render('home/index.html.twig',
            ['groupes' => $groups]
        );
    }
}