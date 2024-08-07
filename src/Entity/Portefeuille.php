<?php

namespace App\Entity;

use App\Repository\PortefeuilleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PortefeuilleRepository::class)
 * @ORM\Table(name="portefeuille")
 */
#[ORM\Entity(repositoryClass: PortefeuilleRepository::class)]
class Portefeuille
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, name: 'fullName')]
    private ?string $fullName = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: 'string', length: 20, name: 'phoneNumber')]
    private ?string $phoneNumber = null;

    #[ORM\Column(type: 'string', length: 255, name: 'downloadURL', nullable: true)]
    private ?string $downloadURL = null;

    #[ORM\Column(type: 'string', length: 50, name: 'userType')]
    private ?string $userType = null;

    #[ORM\Column(type: 'string', length: 20, name: 'numCompte', unique: true)]
    private ?string $numCompte = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $solde = 0.00;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function getDownloadURL(): ?string
    {
        return $this->downloadURL;
    }

    public function setDownloadURL(?string $downloadURL): self
    {
        $this->downloadURL = $downloadURL;
        return $this;
    }

    public function getUserType(): ?string
    {
        return $this->userType;
    }

    public function setUserType(string $userType): self
    {
        $this->userType = $userType;
        return $this;
    }

    public function getNumCompte(): ?string
    {
        return $this->numCompte;
    }

    public function setNumCompte(string $numCompte): self
    {
        $this->numCompte = $numCompte;
        return $this;
    }

    public function getSolde(): float
    {
        return $this->solde;
    }

    public function setSolde(float $solde): self
    {
        $this->solde = $solde;
        return $this;
    }
}
