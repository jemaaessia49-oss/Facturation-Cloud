<?php

namespace App\Controller;

use App\Repository\FactureRepository;
use App\Repository\OffreFinanciereRepository;
use App\Repository\ProjetRepository;
use App\Repository\SocieteRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(
        SocieteRepository $societeRepository,
        ProjetRepository $projetRepository,
        FactureRepository $factureRepository,
        OffreFinanciereRepository $offreFinanciereRepository,
        UserRepository $userRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $annee = (int) date('Y');

        // Statistiques principales
        $factures = $factureRepository->findAll();
        $montantTotal = array_sum(array_map(fn($f) => $f->getMontantTotal(), $factures));

        $stats = [
            'societes' => $societeRepository->count([]),
            'projets' => $projetRepository->count([]),
            'factures' => count($factures),
            'montant_total' => $montantTotal,
            'offres_actives' => $offreFinanciereRepository->count(['active' => true]),
            'utilisateurs' => $userRepository->count([]),
        ];

        // Dernières factures et projets récents
        $facturesRecentes = $factureRepository->findBy([], ['id' => 'DESC'], 5);
        $projetsRecents = $projetRepository->findBy([], ['id' => 'DESC'], 5);

        // Répartition des factures par statut (pour le graphique)
        $facturesParStatut = [];
        foreach ($factures as $facture) {
            $statut = $facture->getStatut();
            $facturesParStatut[$statut] = ($facturesParStatut[$statut] ?? 0) + 1;
        }

        // CA mensuel de l'année en cours (pour le graphique)
        $montantsParMois = array_fill(1, 12, 0.0);
        foreach ($factures as $facture) {
            if ($facture->getAnnee() === $annee) {
                $montantsParMois[$facture->getMois()] += $facture->getMontantTotal();
            }
        }

        $moisLabels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];

        return $this->render('dashboard/index.html.twig', [
            'stats' => $stats,
            'annee' => $annee,
            'factures_recentes' => $facturesRecentes,
            'projets_recents' => $projetsRecents,
            'factures_par_statut' => $facturesParStatut,
            'factures_statut_labels' => array_keys($facturesParStatut),
            'factures_statut_values' => array_values($facturesParStatut),
            'montants_mensuels' => [
                'labels' => $moisLabels,
                'values' => array_values($montantsParMois),
            ],
        ]);
    }
}