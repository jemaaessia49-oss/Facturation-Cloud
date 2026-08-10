<?php

namespace App\Controller\Admin;

use App\Repository\FactureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FactureListController extends AbstractController
{
    #[Route('/admin/factures', name: 'app_admin_facture_list', methods: ['GET'])]
    public function index(FactureRepository $factureRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $factures = $factureRepository->findBy([], ['annee' => 'DESC', 'mois' => 'DESC']);

        return $this->render('admin/facture/index.html.twig', [
            'factures' => $factures,
        ]);
    }
}
