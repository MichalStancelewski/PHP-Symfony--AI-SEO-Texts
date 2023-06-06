<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 380)]
    private ?string $theme = null;

    #[ORM\Column]
    private ?int $length = null;

    #[ORM\Column]
    private ?bool $withTitle = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateCreated = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;
    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $lastChangedDate = null;

    #[ORM\Column(length: 3)]
    private ?string $language = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(int $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function isWithTitle(): ?bool
    {
        return $this->withTitle;
    }

    public function setWithTitle(bool $withTitle): self
    {
        $this->withTitle = $withTitle;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeImmutable
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeImmutable $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

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

    public function getLastChangedDate(): ?\DateTimeInterface
    {
        return $this->lastChangedDate;
    }

    public function setLastChangedDate(\DateTimeInterface $lastChangedDate): self
    {
        $this->lastChangedDate = $lastChangedDate;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }
}
