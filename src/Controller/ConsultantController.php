<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Repository\ProjetRepository;
use App\Service\FacturationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ConsultantController extends AbstractController
{
    #[Route('/consultant', name: 'app_consultant_index')]
    public function index(ProjetRepository $projetRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CONSULTANT');

        return $this->render('consultant/index.html.twig', [
            'projets' => $projetRepository->findAll(),
        ]);
    }

    #[Route('/consultant/projets/{id}/offre/importer', name: 'app_consultant_offre_import', methods: ['GET', 'POST'])]
    public function importerOffre(Projet $projet, Request $request, \App\Service\OffreImportService $importService, \Doctrine\ORM\EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CONSULTANT');

        $form = $this->createForm(\App\Form\OffreImportType::class);
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

            return $this->redirectToRoute('app_consultant_index');
        }

        return $this->render('consultant/offre_import.html.twig', [
            'form' => $form,
            'projet' => $projet,
        ]);
    }
    #[Route('/consultant/projets/{id}/factures', name: 'app_consultant_facture_historique', methods: ['GET'])]
    public function historique(Projet $projet, \App\Repository\FactureRepository $factureRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CONSULTANT');

        $anneeActuelle = (int) date('Y');

        $factures = $factureRepository->findBy(
            ['projet' => $projet, 'annee' => $anneeActuelle],
            ['mois' => 'ASC']
        );

        $facturesParMois = [];
        foreach ($factures as $facture) {
            $facturesParMois[$facture->getMois()] = $facture;
        }

        return $this->render('consultant/historique.html.twig', [
            'projet' => $projet,
            'annee' => $anneeActuelle,
            'factures_par_mois' => $facturesParMois,
        ]);
    }
    #[Route('/consultant/projets/{id}/factures/{annee}/{mois}', name: 'app_consultant_facture_show', requirements: ['mois' => '\d+', 'annee' => '\d+'], methods: ['GET', 'POST'])]
    public function facture(Projet $projet, int $annee, int $mois, Request $request, FacturationService $facturationService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CONSULTANT');

        $facture = $facturationService->obtenirOuCreerFacture($projet, $mois, $annee);

        if ($request->isMethod('POST')) {
            if ($facture->getStatut() === 'validee' && !$this->isGranted('ROLE_ADMIN')) {
                $this->addFlash('danger', 'Cette facture est validee et ne peut plus etre modifiee.');
                return $this->redirectToRoute('app_consultant_facture_show', ['id' => $projet->getId(), 'annee' => $annee, 'mois' => $mois]);
            }

            $quantites = $request->request->all('quantite');

            foreach ($facture->getLigneFactures() as $ligne) {
                if (isset($quantites[$ligne->getId()])) {
                    $ligne->setQuantiteReelle((float) $quantites[$ligne->getId()]);
                }
            }

            $facturationService->recalculerMontantTotal($facture);

            $this->addFlash('success', 'Facture mise a jour.');

            return $this->redirectToRoute('app_consultant_facture_show', ['id' => $projet->getId(), 'annee' => $annee, 'mois' => $mois]);
        }

        return $this->render('consultant/facture.html.twig', [
            'projet' => $projet,
            'facture' => $facture,
            'mois' => $mois,
            'annee' => $annee,
        ]);
    }

    #[Route('/consultant/projets/{id}/factures/{annee}/{mois}/valider', name: 'app_consultant_facture_valider', requirements: ['mois' => '\d+', 'annee' => '\d+'], methods: ['POST'])]
    public function validerFacture(Projet $projet, int $annee, int $mois, Request $request, FacturationService $facturationService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CONSULTANT');

        $facture = $facturationService->obtenirOuCreerFacture($projet, $mois, $annee);

        if ($this->isCsrfTokenValid('valider_facture_'.$facture->getId(), $request->getPayload()->getString('_token'))) {
            $facturationService->validerFacture($facture);
            $this->addFlash('success', 'Facture validee avec succes.');
        }

        return $this->redirectToRoute('app_consultant_facture_show', ['id' => $projet->getId(), 'annee' => $annee, 'mois' => $mois]);
    }
}


