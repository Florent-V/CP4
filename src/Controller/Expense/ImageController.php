<?php

namespace App\Controller\Expense;

use App\Entity\User;
use App\Enum\Role;
use App\Repository\ExpenseRepository;
use App\Service\ExpenseAccessManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Role::USER->value)]
#[Route(
    '/expense-image/{filename}',
    name: 'app_expense_image',
    requirements: ['filename' => '.+'],
    methods: ['GET']
)]
class ImageController extends AbstractController
{
    public function __construct(
        private readonly string $expenseUploadDir,
    ) {
    }

    public function __invoke(
        string $filename,
        ExpenseRepository $expenseRepository,
        ExpenseAccessManager $accessManager,
    ): Response {
        $expense = $expenseRepository->findByPicture($filename);

        if ($expense === null) {
            throw new NotFoundHttpException('Image introuvable.');
        }

        /** @var ?User $user */
        $user = $this->getUser();
        $accessManager->checkMemberAccess($expense->getSplitter(), $user);

        $filePath = $this->expenseUploadDir . '/' . $filename;

        if (!is_file($filePath)) {
            throw new NotFoundHttpException('Fichier introuvable.');
        }

        return new BinaryFileResponse($filePath);
    }
}
