<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(length: 520, nullable: true)]
    private ?string $title = null;

    #[ORM\Column]
    private ?bool $isUsed = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function isIsUsed(): ?bool
    {
        return $this->isUsed;
    }

    public function setIsUsed(bool $isUsed): self
    {
        $this->isUsed = $isUsed;

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

    public function __toString(): string
    {
        return $this->title;
    }

    public function getTitleFromString(): string
    {
        $text = $this->content;
        $firstMark = strpos($text, '<h1>');
        $secondMark = strpos($text, '</h1>');
        if ($firstMark === false || $secondMark === false) {
            //todo throw error
            return 'error';
        } else {
            return trim(substr($text, $firstMark + 4, $secondMark - $firstMark - 4));
        }
    }

    public function getFormatedContentFromString(): string
    {
        $text = trim(preg_replace('/\s\s+/', ' ', $this->content));
        $firstMark = strpos($text, '<h1>');
        $secondMark = strpos($text, '</h1>');
        if ($firstMark === false || $secondMark === false) {
            $text = trim($text);
        } else {
            $text = trim(substr($text, $secondMark + 5, strlen($text)));
        }
        return $text;
    }
}