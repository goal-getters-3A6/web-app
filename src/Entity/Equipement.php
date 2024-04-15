<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use App\Repository\EquipementRepository;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass:EquipementRepository::class)]
class Equipement
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idEq")]
private ?int $idEq=null;



#[ORM\Column(length:255)]
#[Assert\NotBlank(message: "Le nom ne peut pas être vide.")]
private ?string $nomeq = null;

#[ORM\Column(length:255)]
#[Assert\NotBlank(message: "La description ne peut pas être vide.")]
private ?string $desceq = null;

#[ORM\Column(length:255)]
#[Assert\NotBlank(message: "La documentation ne peut pas être vide.")]
private ?string $doceq = null;

#[ORM\Column(length:255)]
private ?string $imageeq = null;

#[ORM\Column(length:255)]
#[Assert\NotBlank(message: "La catégorie ne peut pas être vide.")]
private ?string $categeq = null;

#[ORM\Column]
private ?int $noteeq = null;

#[ORM\Column(length:255)]
#[Assert\NotBlank(message: "La marque ne peut pas être vide.")]
private ?string $marqueeq = null;

#[ORM\Column(length:255 )]
#[Assert\NotBlank(message: "Le matricule ne peut pas être vide.")]
private ?string $matriculeeq = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La date de précédente maintenance est obligatoire.")]
    #[Assert\LessThanOrEqual(value: "today", message: "La date de précédente maintenance doit être égale ou antérieure à la date actuelle.")]
    private ?\DateTime $datepremainte=null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La date de prochaine maintenance est obligatoire.")]
    #[Assert\GreaterThanOrEqual(value: "today", message: "La date de prochaine maintenance doit être postérieure à la date actuelle.")]
    private ?\DateTime $datepromainte=null;



  
    public function getIdEq(): ?int
    {
        return $this->idEq;
    }

    public function getNomeq(): ?string
    {
        return $this->nomeq;
    }

    public function setNomeq(string $nomeq): static
    {
        $this->nomeq = $nomeq;

        return $this;
    }

    public function getDesceq(): ?string
    {
        return $this->desceq;
    }

    public function setDesceq(string $desceq): static
    {
        $this->desceq = $desceq;

        return $this;
    }

    public function getDoceq(): ?string
    {
        return $this->doceq;
    }

    public function setDoceq(string $doceq): static
    {
        $this->doceq = $doceq;

        return $this;
    }

    public function getImageeq(): ?string
    {
        return $this->imageeq;
    }

    public function setImageeq(string $imageeq): static
    {
        $this->imageeq = $imageeq;

        return $this;
    }

    public function getCategeq(): ?string
    {
        return $this->categeq;
    }

    public function setCategeq(string $categeq): static
    {
        $this->categeq = $categeq;

        return $this;
    }

    public function getNoteeq(): ?int
    {
        return $this->noteeq;
    }

    public function setNoteeq(int $noteeq): static
    {
        $this->noteeq = $noteeq;

        return $this;
    }

    public function getMarqueeq(): ?string
    {
        return $this->marqueeq;
    }

    public function setMarqueeq(string $marqueeq): static
    {
        $this->marqueeq = $marqueeq;

        return $this;
    }

    public function getMatriculeeq(): ?string
    {
        return $this->matriculeeq;
    }

    public function setMatriculeeq(string $matriculeeq): static
    {
        $this->matriculeeq = $matriculeeq;

        return $this;
    }

    public function getDatepremainte(): ?\DateTimeInterface
    {
        return $this->datepremainte;
    }

    public function setDatepremainte(\DateTimeInterface $datepremainte): static
    {
        $this->datepremainte = $datepremainte;

        return $this;
    }

    public function getDatepromainte(): ?\DateTimeInterface
    {
        return $this->datepromainte;
    }

    public function setDatepromainte(\DateTimeInterface $datepromainte): static
    {
        $this->datepromainte = $datepromainte;

        return $this;
    }


}
