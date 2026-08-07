<?php

namespace App\Entity;

use App\Repository\LigneFactureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneFactureRepository::class)]
class LigneFacture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $ressource = null;

    #[ORM\Column]
    private ?float $quantiteReelle = null;

    #[ORM\Column]
    private ?float $prixUnitaire = null;

    #[ORM\Column(length: 255)]
    private ?string $unite = null;

    #[ORM\Column]
    private ?float $montantLigne = null;

    #[ORM\ManyToOne(inversedBy: 'ligneFactures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Facture $facture = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRessource(): ?string
    {
        return $this->ressource;
    }

    public function setRessource(string $ressource): static
    {
        $this->ressource = $ressource;

        return $this;
    }

    public function getQuantiteReelle(): ?float
    {
        return $this->quantiteReelle;
    }

    public function setQuantiteReelle(float $quantiteReelle): static
    {
        $this->quantiteReelle = $quantiteReelle;

        return $this;
    }

    public function getPrixUnitaire(): ?float
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(float $prixUnitaire): static
    {
        $this->prixUnitaire = $prixUnitaire;

        return $this;
    }

    public function getUnite(): ?string
    {
        return $this->unite;
    }

    public function setUnite(string $unite): static
    {
        $this->unite = $unite;

        return $this;
    }

    public function getMontantLigne(): ?float
    {
        return $this->montantLigne;
    }

    public function setMontantLigne(float $montantLigne): static
    {
        $this->montantLigne = $montantLigne;

        return $this;
    }

    public function getFacture(): ?Facture
    {
        return $this->facture;
    }

    public function setFacture(?Facture $facture): static
    {
        $this->facture = $facture;

        return $this;
    }
}
