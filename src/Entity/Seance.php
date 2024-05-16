<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SeanceRepository;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass:SeanceRepository::class)]
class Seance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
   // #[ORM\Column]
   //#[ORM\Column(type: "integer")]
    #[ORM\Column(name: "idseance", type: "integer")]
    private ?int $idseance=null;
    
    #[ORM\Column(length:255)]
    private ?string $nom=null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"L'Horaire est vide")]
    private ?\DateTime $horaire=null;

    #[ORM\Column(length:255)]
    private ?string $jourseance=null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"Numero de salle est vide")]
    #[Assert\Range(
        min: 1,
        max: 5,
        notInRangeMessage: "Le numéro de salle doit être compris entre {{ min }} et {{ max }}."
    )]
    private ?int  $numesalle=null;

    #[ORM\Column(length:255)]
    #[Assert\NotBlank(message:"Duree est vide")]
    #[Assert\Length(
        exactMessage: "La durée doit contenir exactement {{ limit }} caractères.",
        max: 5,
        maxMessage: "La durée ne peut pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: '/^\d{2}min$/',
        message: "La durée doit commencer par deux chiffres et se terminer par 'min'."
    )]
    private ?string $duree=null;

    #[ORM\Column(length:255,nullable: true)]
    private ?string $imageseance;

    public function getIdseance(): ?int
    {
        return $this->idseance;
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

    public function getHoraire(): ?\DateTimeInterface
    {
        return $this->horaire;
    }

    public function setHoraire(\DateTimeInterface $horaire): static
    {
        $this->horaire = $horaire;

        return $this;
    }

    public function getJourseance(): ?string
    {
        return $this->jourseance;
    }

    public function setJourseance(string $jourseance): static
    {
        $this->jourseance = $jourseance;

        return $this;
    }

    public function getNumesalle(): ?int
    {
        return $this->numesalle;
    }

    public function setNumesalle(int $numesalle): static
    {
        $this->numesalle = $numesalle;

        return $this;
    }

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(string $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getImageseance(): ?string
    {
        return $this->imageseance;
    }

   /* public function setImageseance(string $imageseance): static
    {
        $this->imageseance = $imageseance;

        return $this;
    }*/
    public function setImageseance(?string $imageseance): self
    {
        $this->imageseance = $imageseance;

        return $this;
    }


}
