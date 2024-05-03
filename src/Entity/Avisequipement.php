<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AvisEquipementRepository;
use App\Entity\User;
use App\Entity\Equipement;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass:AvisEquipementRepository::class)]
class Avisequipement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idaeq =null;

    #[ORM\Column(length:255)]
    #[Assert\NotBlank(message: "Le commentaire ne peut pas être vide.")]
    private ?string $commaeq=null;

    #[ORM\Column(type: "boolean")]
    private ?bool $likes;

    #[ORM\Column(type: "boolean")]
    private ?bool $dislikes;

   // #[ORM\ManyToOne(inversedBy: "aviseEquipement")]
   #[ORM\ManyToOne(targetEntity: User::class)]
   #[ORM\JoinColumn(name: "idUs", referencedColumnName: "id")]
    private ?User $idUs=null;

   // #[ORM\ManyToOne(inversedBy: "avisequipement")]
   #[ORM\ManyToOne(targetEntity: Equipement::class)]
   #[ORM\JoinColumn(name: "idEq", referencedColumnName: "idEq")]
private ?Equipement $idEq;


    public function getIdaeq(): ?int
    {
        return $this->idaeq;
    }

    public function getCommaeq(): ?string
    {
        return $this->commaeq;
    }

    public function setCommaeq(string $commaeq): static
    {
        $this->commaeq = $commaeq;

        return $this;
    }

    public function isLikes(): ?bool
    {
        return $this->likes;
    }

    public function setLikes(bool $likes): static
    {
        $this->likes = $likes;

        return $this;
    }

    public function isDislikes(): ?bool
    {
        return $this->dislikes;
    }

    public function setDislikes(bool $dislikes): static
    {
        $this->dislikes = $dislikes;

        return $this;
    }

    public function getIdUs(): ?User
    {
        return $this->idUs;
    }

    public function setIdUs(?User $idUs): static
    {
        $this->idUs = $idUs;

        return $this;
    }

    public function getIdEq(): ?Equipement
    {
        return $this->idEq;
    }

    public function setIdEq(?Equipement $idEq): static
    {
        $this->idEq = $idEq;

        return $this;
    }


}
