<?php

namespace App\Controller;

use App\Entity\Splitter;
use App\Service\BalanceCalculator;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/splitter')]
class GuestController extends AbstractController
{
    #[Route(
        '/{id}/guest/{unique_id}',
        name: 'app_splitter_guest_show',
        requirements: [
            'id' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$',
            'unique_id' => '^[0-9a-f]{32}$'
        ],
        methods: ['GET']
    )]
    public function show(
        #[MapEntity(mapping: [
            'id' => 'id',
            'unique_id' => 'uniqueId'
        ])]
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
}
