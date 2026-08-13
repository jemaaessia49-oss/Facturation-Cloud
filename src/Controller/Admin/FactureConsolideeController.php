<?php

namespace App\Controller\Admin;

use App\Entity\Societe;
use App\Repository\FactureRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/societes/{id}/facture-consolidee')]
class FactureConsolideeController extends AbstractController
{
    #[Route('/{annee}/{mois}', name: 'app_admin_facture_consolidee', requirements: ['mois' => '\d+', 'annee' => '\d+'], methods: ['GET'])]
    public function show(Societe $societe, int $annee, int $mois, FactureRepository $factureRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        [$projetsAvecFacture, $montantTotalGeneral] = $this->collecterDonnees($societe, $annee, $mois, $factureRepository);

        $moisNoms = ['Janvier','Fevrier','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Decembre'];

        return $this->render('admin/facture/consolidee.html.twig', [
            'societe' => $societe,
            'annee' => $annee,
            'mois' => $mois,
            'moisNoms' => $moisNoms,
            'projets_avec_facture' => $projetsAvecFacture,
            'montant_total_general' => $montantTotalGeneral,
        ]);
    }

    #[Route('/{annee}/{mois}/pdf', name: 'app_admin_facture_consolidee_pdf', requirements: ['mois' => '\d+', 'annee' => '\d+'], methods: ['GET'])]
    public function exporterPdf(Societe $societe, int $annee, int $mois, FactureRepository $factureRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        [$projetsAvecFacture, $montantTotalGeneral] = $this->collecterDonnees($societe, $annee, $mois, $factureRepository);

        $moisNoms = ['Janvier','Fevrier','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Decembre'];

        $html = $this->renderView('admin/facture/consolidee_pdf.html.twig', [
            'societe' => $societe,
            'annee' => $annee,
            'mois' => $mois,
            'moisNoms' => $moisNoms,
            'projets_avec_facture' => $projetsAvecFacture,
            'montant_total_general' => $montantTotalGeneral,
        ]);

        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $nomFichier = sprintf('facture_consolidee_%s_%s_%s.pdf', $societe->getNom(), $mois, $annee);

        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$nomFichier.'"',
            ]
        );
    }

    private function collecterDonnees(Societe $societe, int $annee, int $mois, FactureRepository $factureRepository): array
    {
        $projetsAvecFacture = [];
        $montantTotalGeneral = 0;

        foreach ($societe->getProjets() as $projet) {
            $facture = $factureRepository->findOneBy([
                'projet' => $projet,
                'mois' => $mois,
                'annee' => $annee,
            ]);

            if ($facture !== null) {
                $montantTotalGeneral += $facture->getMontantTotal();
            }

            $projetsAvecFacture[] = [
                'projet' => $projet,
                'facture' => $facture,
            ];
        }

        return [$projetsAvecFacture, $montantTotalGeneral];
    }
}
