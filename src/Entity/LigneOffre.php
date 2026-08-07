<?php

namespace App\Entity;

use App\Repository\LigneOffreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneOffreRepository::class)]
class LigneOffre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $ressource = null;

    #[ORM\Column]
    private ?float $quantite = null;

    #[ORM\Column(length: 255)]
    private ?string $unite = null;

    #[ORM\Column]
    private ?float $prixUnitaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $typeService = null;

    #[ORM\ManyToOne(inversedBy: 'ligneOffres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?OffreFinanciere $offreFinanciere = null;

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

    public function getQuantite(): ?float
    {
        return $this->quantite;
    }

    public function setQuantite(float $quantite): static
    {
        $this->quantite = $quantite;

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

    public function getPrixUnitaire(): ?float
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(float $prixUnitaire): static
    {
        $this->prixUnitaire = $prixUnitaire;

        return $this;
    }

    public function getTypeService(): ?string
    {
        return $this->typeService;
    }

    public function setTypeService(?string $typeService): static
    {
        $this->typeService = $typeService;

        return $this;
    }

    public function getOffreFinanciere(): ?OffreFinanciere
    {
        return $this->offreFinanciere;
    }

    public function setOffreFinanciere(?OffreFinanciere $offreFinanciere): static
    {
        $this->offreFinanciere = $offreFinanciere;

        return $this;
    }
}
