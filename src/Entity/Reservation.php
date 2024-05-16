<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReservationRepository;
use App\Entity\User;
use App\Entity\Seance;

#[ORM\Entity(repositoryClass:ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idreservation=null;


    #[ORM\ManyToOne(targetEntity: Seance::class)]
    #[ORM\JoinColumn(name: 'ids', referencedColumnName: 'idseance')]
    private ?Seance $ids=null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'iduser', referencedColumnName: 'id')]    
    private ?User $iduser=null;

    public function getIdreservation(): ?int
    {
        return $this->idreservation;
    }
    public function getNompersonne(): ?string
    {
        return $this->iduser ? $this->iduser->getNom() : null;
    }
    
    public function getPrenompersonne(): ?string
    {
        return $this->iduser ? $this->iduser->getPrenom() : null;
    }
  

   

    public function getIds(): ?Seance
    {
        return $this->ids;
    }

   public function setIds(?Seance $ids): static
    {
        $this->ids = $ids;

        return $this;
    }

    public function getIduser(): ?User
    {
        return $this->iduser;
    }

    public function setIduser(?User $iduser): static
    {
        $this->iduser = $iduser;

        return $this;
    }


}
