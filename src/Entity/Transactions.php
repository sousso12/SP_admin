<?php

namespace App\Entity;

use App\Repository\TransactionsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TransactionsRepository::class)
 * @ORM\Table(name="transactions")
 */
#[ORM\Entity(repositoryClass: TransactionsRepository::class)]
class Transactions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, name: 'amount')]
    private float $amount;

    #[ORM\Column(type: 'string', length: 20, name: 'status')]
    private ?string $status = null;

    #[ORM\Column(type: 'string', length: 20, unique: true, name: 'numTransaction')]
    private ?string $numTransaction = null;

    #[ORM\Column(type: 'string', length: 20, name: 'numCompteClient')]
    private ?string $numCompteClient = null;

    #[ORM\Column(type: 'string', length: 20, name: 'numCompteMarchand')]
    private ?string $numCompteMarchand = null;

    #[ORM\Column(type: 'datetime', name: 'date')]
    private \DateTimeInterface $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getNumTransaction(): ?string
    {
        return $this->numTransaction;
    }

    public function setNumTransaction(string $numTransaction): self
    {
        $this->numTransaction = $numTransaction;
        return $this;
    }

    public function getNumCompteClient(): ?string
    {
        return $this->numCompteClient;
    }

    public function setNumCompteClient(string $numCompteClient): self
    {
        $this->numCompteClient = $numCompteClient;
        return $this;
    }

    public function getNumCompteMarchand(): ?string
    {
        return $this->numCompteMarchand;
    }

    public function setNumCompteMarchand(string $numCompteMarchand): self
    {
        $this->numCompteMarchand = $numCompteMarchand;
        return $this;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }
}
