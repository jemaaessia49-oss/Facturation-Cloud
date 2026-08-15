<?php

namespace App\Controller\Admin;

use App\Repository\OffreFinanciereRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/offres')]
class OffreListController extends AbstractController
{
    #[Route('', name: 'app_admin_offre_index', methods: ['GET'])]
    public function index(OffreFinanciereRepository $offreRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $offres = $offreRepository->findBy(['active' => true], ['dateImport' => 'DESC']);

        return $this->render('admin/offre/index.html.twig', [
            'offres' => $offres,
        ]);
    }
}
