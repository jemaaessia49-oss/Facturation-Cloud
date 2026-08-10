<?php

namespace App\Controller\Admin;

use App\Entity\Projet;
use App\Repository\ProjetRepository;
use App\Service\FacturationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/projets/{id}/factures')]
class FactureController extends AbstractController
{
    #[Route('/{annee}/{mois}', name: 'app_admin_facture_show', requirements: ['mois' => '\d+', 'annee' => '\d+'], methods: ['GET', 'POST'])]
    public function show(Projet $projet, int $annee, int $mois, Request $request, FacturationService $facturationService, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $facture = $facturationService->obtenirOuCreerFacture($projet, $mois, $annee);

        if ($request->isMethod('POST')) {
            if ($facture->getStatut() === 'validee' && !$this->isGranted('ROLE_ADMIN')) {
                $this->addFlash('danger', 'Cette facture est validee et ne peut plus etre modifiee.');
                return $this->redirectToRoute('app_admin_facture_show', ['id' => $projet->getId(), 'annee' => $annee, 'mois' => $mois]);
            }

            $quantites = $request->request->all('quantite');

            foreach ($facture->getLigneFactures() as $ligne) {
                if (isset($quantites[$ligne->getId()])) {
                    $ligne->setQuantiteReelle((float) $quantites[$ligne->getId()]);
                }
            }

            $facturationService->recalculerMontantTotal($facture);

            $this->addFlash('success', 'Facture mise a jour.');

            return $this->redirectToRoute('app_admin_facture_show', ['id' => $projet->getId(), 'annee' => $annee, 'mois' => $mois]);
        }

        return $this->render('admin/facture/show.html.twig', [
            'projet' => $projet,
            'facture' => $facture,
            'mois' => $mois,
            'annee' => $annee,
        ]);
    }

    #[Route('/{annee}/{mois}/valider', name: 'app_admin_facture_valider', requirements: ['mois' => '\d+', 'annee' => '\d+'], methods: ['POST'])]
    public function valider(Projet $projet, int $annee, int $mois, Request $request, FacturationService $facturationService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $facture = $facturationService->obtenirOuCreerFacture($projet, $mois, $annee);

        if ($this->isCsrfTokenValid('valider_facture_'.$facture->getId(), $request->getPayload()->getString('_token'))) {
            $facturationService->validerFacture($facture);
            $this->addFlash('success', 'Facture validee avec succes.');
        }

        return $this->redirectToRoute('app_admin_facture_show', ['id' => $projet->getId(), 'annee' => $annee, 'mois' => $mois]);
    }
}
