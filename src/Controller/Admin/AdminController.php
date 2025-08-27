<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        // Liste des sections de l’admin à afficher
        $sections = [
            [
                'name' => 'User',
                'route' => 'admin_app_user_index',
                'icon' => 'user.svg',
                'description' => 'Gérer les utilisateurs'
            ],
            [
                'name' => 'Member',
                'route' => 'admin_app_member_index',
                'icon' => 'member.svg',
                'description' => 'Gérer les membres'
            ],
            [
                'name' => 'Splitter',
                'route' => 'admin_app_splitter_index',
                'icon' => 'splitter.svg',
                'description' => 'Gérer les splitters'
            ],
            [
                'name' => 'Expense',
                'route' => 'admin_app_expense_index',
                'icon' => 'expense.svg',
                'description' => 'Gérer les dépenses'
            ],
            [
                'name' => 'Expense Category',
                'route' => 'admin_app_expense_category_index',
                'icon' => 'expense-category.svg',
                'description' => 'Gérer les catégories de dépenses'
            ],
            [
                'name' => 'Splitter Category',
                'route' => 'admin_app_splitter_category_index',
                'icon' => 'splitter-category.svg',
                'description' => 'Gérer les catégories de splitter'
            ],
        ];

        return $this->render('/admin/index.html.twig', [
            'sections' => $sections,
        ]);
    }
}
