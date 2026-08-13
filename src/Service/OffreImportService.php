<?php

namespace App\Service;

use App\Entity\LigneOffre;
use App\Entity\OffreFinanciere;
use App\Entity\Projet;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Smalot\PdfParser\Parser as PdfParser;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class OffreImportService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    public function importer(Projet $projet, UploadedFile $fichier): OffreFinanciere
    {
        $extension = strtolower($fichier->getClientOriginalExtension());

        if ($extension === 'pdf') {
            return $this->importDepuisPdf($projet, $fichier);
        }

        return $this->importDepuisExcel($projet, $fichier);
    }

    public function importDepuisExcel(Projet $projet, UploadedFile $fichier): OffreFinanciere
    {
        $offre = $this->creerOffre($projet, $fichier);

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

            $this->creerLigneOffre(
                $offre,
                (string) $ligne[0],
                (float) $ligne[1],
                (string) $ligne[2],
                (float) $ligne[3],
                $ligne[4] ?? null
            );
        }

        $this->em->flush();

        return $offre;
    }

    public function importDepuisPdf(Projet $projet, UploadedFile $fichier): OffreFinanciere
    {
        $offre = $this->creerOffre($projet, $fichier);

        $parser = new PdfParser();
        $document = $parser->parseFile($fichier->getPathname());
        $texte = $document->getText();

        $lignes = preg_split('/\r\n|\r|\n/', $texte);
        // Pattern : Ressource (texte) + Quantite (nombre) + Unite (lettres, collees ou non)
        // + Prix unitaire (nombre) + Type de service optionnel (lettres)
        $pattern = '/^(.+?)\s+(\d+(?:[.,]\d+)?)\s*([a-zA-ZÀ-ÿ]+)\s+(\d+(?:[.,]\d+)?)\s*([a-zA-ZÀ-ÿ\s]*)$/u';

        $nombreLignesImportees = 0;

        foreach ($lignes as $ligneTexte) {
            $ligneTexte = trim($ligneTexte);

            if ($ligneTexte === '') {
                continue;
            }

            if (!preg_match($pattern, $ligneTexte, $matches)) {
                continue;
            }

            $ressource = trim($matches[1]);
            $quantite = (float) str_replace(',', '.', $matches[2]);
            $unite = trim($matches[3]);
            $prixUnitaire = (float) str_replace(',', '.', $matches[4]);
            $typeService = trim($matches[5]);

            $this->creerLigneOffre(
                $offre,
                $ressource,
                $quantite,
                $unite,
                $prixUnitaire,
                $typeService !== '' ? $typeService : null
            );

            $nombreLignesImportees++;
        }

        if ($nombreLignesImportees === 0) {
            throw new \RuntimeException('Aucune ligne exploitable n\'a ete trouvee dans ce PDF. Verifiez que le fichier contient un tableau simple (une ressource par ligne).');
        }

        $this->em->flush();

        return $offre;
    }

    private function creerOffre(Projet $projet, UploadedFile $fichier): OffreFinanciere
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

        return $offre;
    }

    private function creerLigneOffre(OffreFinanciere $offre, string $ressource, float $quantite, string $unite, float $prixUnitaire, ?string $typeService): void
    {
        $ligneOffre = new LigneOffre();
        $ligneOffre->setOffreFinanciere($offre);
        $ligneOffre->setRessource($ressource);
        $ligneOffre->setQuantite($quantite);
        $ligneOffre->setUnite($unite);
        $ligneOffre->setPrixUnitaire($prixUnitaire);
        $ligneOffre->setTypeService($typeService);

        $this->em->persist($ligneOffre);
    }
}

