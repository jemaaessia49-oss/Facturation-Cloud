<?php

namespace App\Controller\Admin;

use App\Entity\Projet;
use App\Form\OffreImportType;
use App\Service\OffreImportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/projets/{id}/offre')]
class OffreImportController extends AbstractController
{
    #[Route('/importer', name: 'app_admin_offre_import', methods: ['GET', 'POST'])]
    public function import(Projet $projet, Request $request, OffreImportService $importService, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CONSULTANT');

        $form = $this->createForm(OffreImportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fichier = $form->get('fichier')->getData();

            try {
                $offre = $importService->importer($projet, $fichier);

                $nombreLignes = (int) $em->getConnection()->fetchOne(
                    'SELECT COUNT(*) FROM ligne_offre WHERE offre_financiere_id = ?',
                    [$offre->getId()]
                );

                $this->addFlash('success', sprintf(
                    'Offre financiere importee avec succes (%d lignes, version %d).',
                    $nombreLignes,
                    $offre->getVersion()
                ));
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Erreur lors de l\'import : ' . $e->getMessage());
            }

            return $this->redirectToRoute('app_admin_projet_edit', ['id' => $projet->getId()]);
        }

        return $this->render('admin/offre/import.html.twig', [
            'form' => $form,
            'projet' => $projet,
        ]);
    }
}
