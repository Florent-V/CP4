<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Enum\Role;
use App\Repository\CategoryRepository;
use App\Repository\ExpenseRepository;
use App\Repository\SplitterRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CsvController extends AbstractController
{
    #[IsGranted(Role::ADMIN->value)]
    #[Route('/upload-csv', name: 'app_upload_csv', methods: ['GET', 'POST'])]
    public function uploadCsv(
        Request $request,
        SplitterRepository $splitterRepository,
        UserRepository $userRepository,
        CategoryRepository $categoryRepository,
        ExpenseRepository $expenseRepository,
    ): Response {

        $jsonResult = null;
        $jsonResultString = null;

        if ($request->isMethod('POST')) {
            $file = $request->files->get('csv_file');

            //if ($file instanceof UploadedFile && $file->getClientOriginalExtension() === 'csv') {
            try {
                // Lire le fichier CSV
                $csvData = file_get_contents($file->getPathname());
                $rows = array_map('str_getcsv', explode("\n", $csvData));
                $header = array_shift($rows); // Récupère les en-têtes

                // Conversion en JSON
                $jsonResult = [];
                foreach ($rows as $row) {
                    if (count($row) === count($header)) {
                        $jsonResult[] = array_combine($header, $row);
                    }
                }

                $jsonResultString = json_encode($jsonResult, JSON_PRETTY_PRINT);

                $splitter = $splitterRepository->findOneBy([
                    'name' => 'Vie Quotidienne'
                ]);
                $user = $userRepository->findOneBy([
                    'email' => 'vasseurflorent@gmail.com'
                ]);
                $appUser = $user->getAppUser();
                $members = $splitter->getMembers();

                $category = $categoryRepository->findOneBy([
                    'name' => 'Autre'
                ]);

                foreach ($jsonResult as $exp) {
                    $expense = new Expense();
                    $expense->setName($exp['Title']);
                    $expense->setAmount((float) $exp['Amount']);
                    $expense->setCreatedAt(new DateTime($exp['Date & time']));
                    $expense->setMadeAt(new DateTime($exp['Date & time']));
                    $expense->setDevise('€');
                    $expense->setAddedBy($appUser);
                    $expense->setPaidBy($exp['Paid by'] === 'Florent' ? $members[0] : $members[1]);
                    $expense->setSplitter($splitter);
                    $expense->setCategory($category);
                    if ((float) $exp['Impacted to Florent'] < 0) {
                        $expense->addBeneficiary($members[0]);
                    }
                    if ((float) $exp['Impacted to Julia'] < 0) {
                        $expense->addBeneficiary($members[1]);
                    }

                    $expenseRepository->save($expense, true);
                }
            } catch (FileException $e) {
                $this->addFlash('error', 'Erreur lors du chargement du fichier : ' . $e->getMessage());
            }
//            } else {
//                $this->addFlash('error', 'Veuillez charger un fichier CSV valide.');
//            }
        }

        // dump('type of jsonResult', gettype($jsonResult));
        // dump('type of jsonResultString', gettype($jsonResultString));

        return $this->render('csv/upload.html.twig', [
            'jsonResult' => $jsonResultString,
        ]);
    }
}
