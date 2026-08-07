<?php

namespace App\Entity;

use App\Repository\OffreFinanciereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OffreFinanciereRepository::class)]
class OffreFinanciere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $dateImport = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fichierSource = null;

    #[ORM\Column]
    private ?int $version = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\ManyToOne(inversedBy: 'offreFinancieres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Projet $projet = null;

    /**
     * @var Collection<int, LigneOffre>
     */
    #[ORM\OneToMany(targetEntity: LigneOffre::class, mappedBy: 'offreFinanciere', orphanRemoval: true)]
    private Collection $ligneOffres;

    public function __construct()
    {
        $this->ligneOffres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateImport(): ?\DateTime
    {
        return $this->dateImport;
    }

    public function setDateImport(\DateTime $dateImport): static
    {
        $this->dateImport = $dateImport;

        return $this;
    }

    public function getFichierSource(): ?string
    {
        return $this->fichierSource;
    }

    public function setFichierSource(?string $fichierSource): static
    {
        $this->fichierSource = $fichierSource;

        return $this;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion(int $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getProjet(): ?Projet
    {
        return $this->projet;
    }

    public function setProjet(?Projet $projet): static
    {
        $this->projet = $projet;

        return $this;
    }

    /**
     * @return Collection<int, LigneOffre>
     */
    public function getLigneOffres(): Collection
    {
        return $this->ligneOffres;
    }

    public function addLigneOffre(LigneOffre $ligneOffre): static
    {
        if (!$this->ligneOffres->contains($ligneOffre)) {
            $this->ligneOffres->add($ligneOffre);
            $ligneOffre->setOffreFinanciere($this);
        }

        return $this;
    }

    public function removeLigneOffre(LigneOffre $ligneOffre): static
    {
        if ($this->ligneOffres->removeElement($ligneOffre)) {
            // set the owning side to null (unless already changed)
            if ($ligneOffre->getOffreFinanciere() === $this) {
                $ligneOffre->setOffreFinanciere(null);
            }
        }

        return $this;
    }
}
