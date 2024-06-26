<?php

namespace App\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['mail'], message: 'There is already an account with this mail')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, PasswordUpgraderInterface, TwoFactorInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string  $prenom = null;

    #[ORM\Column(type: 'string', length: 255, unique: true), Assert\Email]
    private ?string $mail = null;

    #[ORM\Column(length: 255)]
    private ?string $mdp = null;

    #[ORM\Column]
    private ?bool $statut = false;

    #[ORM\Column]
    private ?int $nbTentative = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column]
    private ?\DateTime $dateNaissance = null;

    #[ORM\Column]
    private ?\DateTime $dateInscription = null;

    #[ORM\Column(length: 255)]
    private ?string $tel = null;

    #[ORM\Column(length: 255)]
    private ?string $role = null;

    #[ORM\Column]
    private ?float $poids = null;

    #[ORM\Column]
    private ?float $taille = null;

    #[ORM\Column(length: 255)]
    private ?string $sexe = null;

    #[ORM\Column]
    private ?int $tfa = null;

    #[ORM\Column(length: 255)]
    private ?string $tfaSecret = null;

    #[ORM\Column(type: 'boolean', name: 'isVerified', options: ['default' => '0'])]
    private $isVerified = false;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $activation_token;

    #[ORM\Column(type: 'string', length: 60, nullable: true)]
    private $reset_token;

    #[ORM\Column(type: 'string', length: 60, nullable: true)]
    private $disable_token;

    #[ORM\Column(type: 'integer', name: "verificationCode", nullable: true)]
    private $verificationCode;

    /**
     * @ORM\Column(name="googleAuthenticatorSecret", type="string", nullable=true)
     */
    private $googleAuthenticatorSecret;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): static
    {
        $this->mdp = $mdp;

        return $this;
    }

    public function isStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getNbTentative(): ?int
    {
        return $this->nbTentative;
    }

    public function setNbTentative(int $nbTentative): static
    {
        $this->nbTentative = $nbTentative;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): static
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTimeInterface $dateInscription): static
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): static
    {
        $this->tel = $tel;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getPoids(): ?float
    {
        return $this->poids;
    }

    public function setPoids(float $poids): static
    {
        $this->poids = $poids;

        return $this;
    }

    public function getTaille(): ?float
    {
        return $this->taille;
    }

    public function setTaille(float $taille): static
    {
        $this->taille = $taille;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): static
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getTfa(): ?int
    {
        return $this->tfa;
    }

    public function setTfa(int $tfa): static
    {
        $this->tfa = $tfa;

        return $this;
    }

    public function getTfaSecret(): ?string
    {
        return $this->tfaSecret;
    }

    public function setTfaSecret(string $tfaSecret): static
    {
        $this->tfaSecret = $tfaSecret;

        return $this;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }




    public function getUserIdentifier(): ?string
    {
        return $this->id;
    }
    public function __toString(): string
    {
        return $this->id;
    }
    public function serialize()
    {
        return serialize($this->id);
    }
    public function unserialize($data)
    {
        $this->id = unserialize($data);
    }
    /**
     * Get the value of isVerified
     */
    public function getIsVerified()
    {
        return $this->isVerified;
    }
    /**
     * Set the value of isVerified
     *
     * @return  self
     */
    public function setIsVerified($isVerified)
    {
        $this->isVerified = $isVerified;

        return $this;
    }
    /**
     * Get the value of activation_token
     */
    public function getActivationToken()
    {
        return $this->activation_token;
    }
    /**
     * Set the value of activation_token
     *
     * @return  self
     */
    public function setActivationToken($activation_token)
    {
        $this->activation_token = $activation_token;

        return $this;
    }
    /**
     * Get the value of reset_token
     */
    public function getResetToken()
    {
        return $this->reset_token;
    }
    /**
     * Set the value of reset_token
     *
     * @return  self
     */
    public function setResetToken($reset_token)
    {
        $this->reset_token = $reset_token;

        return $this;
    }
    /**
     * Get the value of disable_token
     */
    public function getDisableToken()
    {
        return $this->disable_token;
    }
    /**
     * Set the value of disable_token
     *
     * @return  self
     */
    public function setDisableToken($disable_token)
    {
        $this->disable_token = $disable_token;

        return $this;
    }
    /**
     * Get the value of verificationCode
     */
    public function getVerificationCode()
    {
        return $this->verificationCode;
    }
    /**
     * Set the value of verificationCode
     *
     * @return  self
     */
    public function setVerificationCode($verificationCode)
    {
        $this->verificationCode = $verificationCode;

        return $this;
    }

    public function getUsername(): string
    {
        return (string) $this->mail;
    }

    public function getPassword(): string
    {
        return (string) $this->mdp;
    }

    public function getRoles(): array
    {
        $role = $this->role;
        if (strcmp($role, "CLIENT") == 0) {
            return
                array("ROLE_USER");
        } else {
            return
                array("ROLE_ADMIN");
        }
    }

    public function upgradePassword($user, $newHashedPassword)
    {
        if ($user instanceof User) {
            $user->setMdp($newHashedPassword);
        }
    }

    public function isGoogleAuthenticatorEnabled(): bool
    {
        return null !== $this->tfaSecret;
    }

    public function getGoogleAuthenticatorUsername(): string
    {
        return $this->mail;
    }

    public function getGoogleAuthenticatorSecret(): ?string
    {
        return $this->tfaSecret;
    }

    public function setGoogleAuthenticatorSecret(?string $googleAuthenticatorSecret): void
    {
        $this->tfaSecret = $googleAuthenticatorSecret;
    }

    function getJsonData()
    {
        $var = get_object_vars($this);
        foreach ($var as &$value) {
            if (is_object($value) && method_exists($value, 'getJsonData')) {
                $value = $value->getJsonData();
            }
        }
        return $var;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }
}
