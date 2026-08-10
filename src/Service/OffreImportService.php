<?php

namespace App\Service;

use App\Entity\LigneOffre;
use App\Entity\OffreFinanciere;
use App\Entity\Projet;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class OffreImportService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    public function importDepuisExcel(Projet $projet, UploadedFile $fichier): OffreFinanciere
    {
        foreach ($projet->getOffreFinancieres() as $ancienneOffre) {
            if ($ancienneOffre->isActive()) {
                $ancienneOffre->setActive(false);
            }
        }

        $version = count($projet->getOffreFinancieres()) + 1;

        $offre = new OffreFinanciere();
        $offre->setProjet($projet);
        $offre->setDateImport(new \DateTime());
        $offre->setFichierSource($fichier->getClientOriginalName());
        $offre->setVersion($version);
        $offre->setActive(true);

        $this->em->persist($offre);

        $spreadsheet = IOFactory::load($fichier->getPathname());
        $feuille = $spreadsheet->getActiveSheet();
        $lignes = $feuille->toArray(null, true, true, false);

        $premiereLigne = true;

        foreach ($lignes as $ligne) {
            if ($premiereLigne) {
                $premiereLigne = false;
                continue;
            }

            if (empty($ligne[0])) {
                continue;
            }

            $ligneOffre = new LigneOffre();
            $ligneOffre->setOffreFinanciere($offre);
            $ligneOffre->setRessource((string) $ligne[0]);
            $ligneOffre->setQuantite((float) $ligne[1]);
            $ligneOffre->setUnite((string) $ligne[2]);
            $ligneOffre->setPrixUnitaire((float) $ligne[3]);
            $ligneOffre->setTypeService($ligne[4] ?? null);

            $this->em->persist($ligneOffre);
        }

        $this->em->flush();

        return $offre;
    }
}
