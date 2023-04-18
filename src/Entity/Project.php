<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $status = null;

    #[ORM\Column]
    private ?int $numberOfArticles = null;

    #[ORM\Column]
    private ?bool $withTitle = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date = null;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Article::class)]
    private Collection $articles;

    #[ORM\Column(length: 380)]
    private ?string $theme = null;

    #[ORM\Column]
    private ?int $textsLength = null;

    public function __construct(string $theme, int $numberOfArticles, int $textsLength, bool $withTitle)
    {
        $this->articles = new ArrayCollection();
        $this->status = "pending";
        $this->numberOfArticles = $numberOfArticles;
        $this->textsLength = $textsLength;
        $this->withTitle = $withTitle;
        $this->theme = $theme;
        $this->date = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Warsaw'));
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNumberOfArticles(): ?int
    {
        return $this->numberOfArticles;
    }

    public function setNumberOfArticles(int $numberOfArticles): self
    {
        $this->numberOfArticles = $numberOfArticles;

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

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setProject($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getProject() === $this) {
                $article->setProject(null);
            }
        }

        return $this;
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

    public function getTextsLength(): ?int
    {
        return $this->textsLength;
    }

    public function setTextsLength(int $textsLength): self
    {
        $this->textsLength = $textsLength;

        return $this;
    }
}
