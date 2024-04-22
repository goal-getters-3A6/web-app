<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReclamationRepository;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert; 

#[ORM\Entity(repositoryClass:ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idrec=null;

    #[ORM\Column(length:255)]
    private ?string $categorierec=null;

    #[ORM\Column(length:255)]
    #[Assert\NotBlank(message: "Veuillez saisir une description")]
    private ?string $descriptionrec=null;
    
   /* #[ORM\Column(length:255)]
    private ?string $descriptionrec;*/

    #[ORM\Column(length:255)]
    private ?string $piecejointerec=null;

   /* #[ORM\Column(length:255)]
    private ?string $oddrec=null;*/
    
    #[ORM\Column(length:255)]
    #[Assert\Length(max:255, maxMessage:"L'identifiant ODD ne peut pas dépasser {{ limit }} caractères")]
    private ?string $oddrec=null;

    #[ORM\Column(length:255)]
    private ?string $servicerec=null;

    #[ORM\Column]
    private ?int $etatrec=null;

  
    // #[ORM\ManyToOne(inversedBy: "abonnements")]
   #[ORM\ManyToOne(targetEntity: User::class)]
   #[ORM\JoinColumn(name: "idu", referencedColumnName: "id")]
    private ?User $idu=null;

    public function getIdrec(): ?int
    {
        return $this->idrec;
    }

    public function getCategorierec(): ?string
    {
        return $this->categorierec;
    }

    public function setCategorierec(string $categorierec): static
    {
        $this->categorierec = $categorierec;

        return $this;
    }

    public function getDescriptionrec(): ?string
    {
        return $this->descriptionrec;
    }

    public function setDescriptionrec(string $descriptionrec): static
    {
        $this->descriptionrec = $descriptionrec;

        return $this;
    }

   public function getPiecejointerec(): ?string
    {
        return $this->piecejointerec;
    }

    public function setPiecejointerec(string $piecejointerec): static
    {
        $this->piecejointerec = $piecejointerec;

        return $this;
    }

    public function getOddrec(): ?string
    {
        return $this->oddrec;
    }

    public function setOddrec(string $oddrec): static
    {
        $this->oddrec = $oddrec;

        return $this;
    }

    public function getServicerec(): ?string
    {
        return $this->servicerec;
    }

    public function setServicerec(string $servicerec): static
    {
        $this->servicerec = $servicerec;

        return $this;
    }

    public function getEtatrec(): ?int
    {
        return $this->etatrec;
    }

    public function setEtatrec(int $etatrec): static
    {
        $this->etatrec = $etatrec;

        return $this;
    }

    public function getIdu(): ?User
    {
        return $this->idu;
    }

    public function setIdu(?User $idu): static
    {
        $this->idu = $idu;

        return $this;
    }
    

}
