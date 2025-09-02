<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        // Liste des sections de l’admin à afficher
        $sections = [
            [
                'name' => 'User',
                'route' => 'admin_app_user_index',
                'icon' => 'mdi:user',
                'description' => 'Gérer les utilisateurs'
            ],
            [
                'name' => 'Member',
                'route' => 'admin_app_member_index',
                'icon' => 'material-symbols:group',
                'description' => 'Gérer les membres'
            ],
            [
                'name' => 'Splitter',
                'route' => 'admin_app_splitter_index',
                'icon' => 'material-symbols:ad-group-rounded',
                'description' => 'Gérer les splitters'
            ],
            [
                'name' => 'Expense',
                'route' => 'admin_app_expense_index',
                'icon' => 'arcticons:expense-manager-2',
                'description' => 'Gérer les dépenses'
            ],
            [
                'name' => 'Expense Category',
                'route' => 'admin_app_expense_category_index',
                'icon' => 'mdi:tag',
                'description' => 'Gérer les catégories de dépenses'
            ],
            [
                'name' => 'Splitter Category',
                'route' => 'admin_app_splitter_category_index',
                'icon' => 'mdi:category-plus-outline',
                'description' => 'Gérer les catégories de splitter'
            ],
        ];

        return $this->render('/admin/index.html.twig', [
            'sections' => $sections,
        ]);
    }
}
