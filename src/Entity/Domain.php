<?php

namespace App\Entity;

use App\Repository\DomainRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DomainRepository::class)]
class Domain
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 512)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'domains')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DomainGroup $domainGroup = null;

    public function __construct(string $name)
    {
        $this->name = trim($name);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDomainGroup(): ?DomainGroup
    {
        return $this->domainGroup;
    }

    public function setDomainGroup(?DomainGroup $domainGroup): self
    {
        $this->domainGroup = $domainGroup;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
