<?php

namespace App\Entity;

use App\Repository\ProjetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjetRepository::class)]
class Projet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $numeroSo = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateDebut = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\ManyToOne(inversedBy: 'projets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Societe $societe = null;

    /**
     * @var Collection<int, OffreFinanciere>
     */
    #[ORM\OneToMany(targetEntity: OffreFinanciere::class, mappedBy: 'projet')]
    private Collection $offreFinancieres;

    /**
     * @var Collection<int, Facture>
     */
    #[ORM\OneToMany(targetEntity: Facture::class, mappedBy: 'projet')]
    private Collection $factures;

    public function __construct()
    {
        $this->offreFinancieres = new ArrayCollection();
        $this->factures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroSo(): ?string
    {
        return $this->numeroSo;
    }

    public function setNumeroSo(string $numeroSo): static
    {
        $this->numeroSo = $numeroSo;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateDebut(): ?\DateTime
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTime $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getSociete(): ?Societe
    {
        return $this->societe;
    }

    public function setSociete(?Societe $societe): static
    {
        $this->societe = $societe;

        return $this;
    }

    /**
     * @return Collection<int, OffreFinanciere>
     */
    public function getOffreFinancieres(): Collection
    {
        return $this->offreFinancieres;
    }

    public function addOffreFinanciere(OffreFinanciere $offreFinanciere): static
    {
        if (!$this->offreFinancieres->contains($offreFinanciere)) {
            $this->offreFinancieres->add($offreFinanciere);
            $offreFinanciere->setProjet($this);
        }

        return $this;
    }

    public function removeOffreFinanciere(OffreFinanciere $offreFinanciere): static
    {
        if ($this->offreFinancieres->removeElement($offreFinanciere)) {
            // set the owning side to null (unless already changed)
            if ($offreFinanciere->getProjet() === $this) {
                $offreFinanciere->setProjet(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Facture>
     */
    public function getFactures(): Collection
    {
        return $this->factures;
    }

    public function addFacture(Facture $facture): static
    {
        if (!$this->factures->contains($facture)) {
            $this->factures->add($facture);
            $facture->setProjet($this);
        }

        return $this;
    }

    public function removeFacture(Facture $facture): static
    {
        if ($this->factures->removeElement($facture)) {
            // set the owning side to null (unless already changed)
            if ($facture->getProjet() === $this) {
                $facture->setProjet(null);
            }
        }

        return $this;
    }
}
