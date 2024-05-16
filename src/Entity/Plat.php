<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PlatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass:PlatRepository::class)]
class Plat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idp = null;

    #[ORM\Column(length:255)]
    private ?string $nomp = null;

    #[ORM\Column(type:"float")]
    private ?float $prixp = null;

    #[ORM\Column(length:255)]
    private ?string $descp = null;

    #[ORM\Column(length:255)]
    private ?string $alergiep = null;

    #[ORM\Column(type:"boolean")]
    private ?bool $etatp = false;

    #[ORM\Column(length:255)]
    private ?string $photop = 'oooo';

    #[ORM\Column(type:"integer")]
    private ?int $calories = null;

    /**
     * @ORM\OneToMany(targetEntity=Avisp::class, mappedBy="idplat", orphanRemoval=true)
     */
    private $avisp;

 /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="favoritedPlats")
     */
    private $favoritedBy;

    public function __construct()
    {
        $this->avisp = new ArrayCollection();
        $this->favoritedBy = new ArrayCollection();
    }


    public function getIdp(): ?int
    {
        return $this->idp;
    }

    public function getNomp(): ?string
    {
        return $this->nomp;
    }

    public function setNomp(string $nomp): static
    {
        $this->nomp = $nomp;

        return $this;
    }

    public function getPrixp(): ?float
    {
        return $this->prixp;
    }

    public function setPrixp(float $prixp): static
    {
        $this->prixp = $prixp;

        return $this;
    }

    public function getDescp(): ?string
    {
        return $this->descp;
    }

    public function setDescp(string $descp): static
    {
        $this->descp = $descp;

        return $this;
    }

    public function getAlergiep(): ?string
    {
        return $this->alergiep;
    }

    public function setAlergiep(string $alergiep): static
    {
        $this->alergiep = $alergiep;

        return $this;
    }

    public function isEtatp(): ?bool
    {
        return $this->etatp;
    }

    public function setEtatp(bool $etatp): static
    {
        $this->etatp = $etatp;

        return $this;
    }

    public function getPhotop(): ?string
    {
        return $this->photop;
    }

    public function setPhotop(string $photop): static
    {
        $this->photop = $photop;

        return $this;
    }

    public function getCalories(): ?int
    {
        return $this->calories;
    }

    public function setCalories(int $calories): static
    {
        $this->calories = $calories;

        return $this;
    }

    /**
     * @return Collection|Avisp[]
     */
    public function getAvisp(): Collection
    {
        return $this->avisp;
    }

    public function getIdplat(): ?Plat
    {
        return $this->idp;
    }

    public function setIdplat(?Plat $idplat): self
    {
        $this->idp = $idplat;

        return $this;
    }

/**
     * @return Collection|User[]
     */
    public function getFavoritedBy(): Collection
    {
        return $this->favoritedBy;
    }
}
