<?php

namespace App\Service;

use App\Entity\Facture;
use App\Entity\LigneFacture;
use App\Entity\Projet;
use App\Repository\FactureRepository;
use Doctrine\ORM\EntityManagerInterface;

class FacturationService
{
    public function __construct(
        private EntityManagerInterface $em,
        private FactureRepository $factureRepository
    ) {
    }

    public function obtenirOuCreerFacture(Projet $projet, int $mois, int $annee): Facture
    {
        // Cherche si la facture existe deja pour ce mois
        $facture = $this->factureRepository->findOneBy([
            'projet' => $projet,
            'mois' => $mois,
            'annee' => $annee,
        ]);

        if ($facture !== null) {
            return $facture;
        }

        // Cree la nouvelle facture
        $facture = new Facture();
        $facture->setProjet($projet);
        $facture->setMois($mois);
        $facture->setAnnee($annee);
        $facture->setStatut('brouillon');
        $facture->setMontantTotal(0);

        $this->em->persist($facture);

        // Cherche la facture du mois precedent pour reprendre les consommations
        [$moisPrecedent, $anneePrecedente] = $this->calculerMoisPrecedent($mois, $annee);

        $facturePrecedente = $this->factureRepository->findOneBy([
            'projet' => $projet,
            'mois' => $moisPrecedent,
            'annee' => $anneePrecedente,
        ]);

        if ($facturePrecedente !== null) {
            // Reprend les lignes du mois precedent
            foreach ($facturePrecedente->getLigneFactures() as $ligne) {
                $nouvelleLigne = new LigneFacture();
                $nouvelleLigne->setFacture($facture);
                $nouvelleLigne->setRessource($ligne->getRessource());
                $nouvelleLigne->setUnite($ligne->getUnite());
                $nouvelleLigne->setPrixUnitaire($ligne->getPrixUnitaire());
                $nouvelleLigne->setQuantiteReelle($ligne->getQuantiteReelle());
                $nouvelleLigne->setMontantLigne($ligne->getQuantiteReelle() * $ligne->getPrixUnitaire());

                $this->em->persist($nouvelleLigne);
            }
        } else {
            // Premier mois : initialise depuis l'offre financiere active
            $offreActive = null;
            foreach ($projet->getOffreFinancieres() as $offre) {
                if ($offre->isActive()) {
                    $offreActive = $offre;
                    break;
                }
            }

            if ($offreActive !== null) {
                foreach ($offreActive->getLigneOffres() as $ligneOffre) {
                    $nouvelleLigne = new LigneFacture();
                    $nouvelleLigne->setFacture($facture);
                    $nouvelleLigne->setRessource($ligneOffre->getRessource());
                    $nouvelleLigne->setUnite($ligneOffre->getUnite());
                    $nouvelleLigne->setPrixUnitaire($ligneOffre->getPrixUnitaire());
                    $nouvelleLigne->setQuantiteReelle(0);
                    $nouvelleLigne->setMontantLigne(0);

                    $this->em->persist($nouvelleLigne);
                }
            }
        }

        $this->em->flush();

        $this->recalculerMontantTotal($facture);

        return $facture;
    }

    public function recalculerMontantTotal(Facture $facture): void
    {
        $total = 0;
        foreach ($facture->getLigneFactures() as $ligne) {
            $montantLigne = $ligne->getQuantiteReelle() * $ligne->getPrixUnitaire();
            $ligne->setMontantLigne($montantLigne);
            $total += $montantLigne;
        }

        $facture->setMontantTotal($total);
        $this->em->flush();
    }

    public function validerFacture(Facture $facture): void
    {
        $facture->setStatut('validee');
        $facture->setDateValidation(new \DateTime());
        $this->em->flush();
    }

    private function calculerMoisPrecedent(int $mois, int $annee): array
    {
        if ($mois === 1) {
            return [12, $annee - 1];
        }

        return [$mois - 1, $annee];
    }
}
