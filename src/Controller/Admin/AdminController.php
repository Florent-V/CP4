<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        // Liste des sections de l’admin à afficher
        $sections = [
            ['name' => 'User', 'route' => 'admin_app_user_index'],
            ['name' => 'Member', 'route' => 'admin_app_member_index'],
            ['name' => 'Splitter', 'route' => 'admin_app_splitter_index'],
            ['name' => 'Expense', 'route' => 'admin_app_expense_index'],
            ['name' => 'Expense Category', 'route' => 'admin_app_expense_category_index'],
            ['name' => 'Splitter Category', 'route' => 'admin_app_splitter_category_index'],
        ];

        return $this->render('/admin/index.html.twig', [
            'sections' => $sections,
        ]);
    }
}
