<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(length: 520)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Task::class, orphanRemoval: true)]
    private Collection $tasks;

    #[ORM\Column(length: 3)]
    private ?string $language = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $cardHeader = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cardCompanyName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cardCompanyPhone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cardCompanyEmail = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cardCompanyWebsite = null;

    public function __construct(string $name, string $theme, int $numberOfArticles, int $textsLength, bool $withTitle, string $language)
    {
        $this->articles = new ArrayCollection();
        $this->status = "pending";
        $this->numberOfArticles = $numberOfArticles;
        $this->textsLength = $textsLength;
        $this->withTitle = $withTitle;
        $this->language = $language;
        $this->theme = $theme;
        $this->name = $name;
        $this->date = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Warsaw'));
        $this->tasks = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setProject($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getProject() === $this) {
                $task->setProject(null);
            }
        }

        return $this;
    }

    public function makeUsed(ArticleRepository $articleRepository): void
    {
        foreach ($this->getArticles() as $article){
            $articleRepository->setIsUsed($article, true);
        }
    }

    public function deleteArticles(ArticleRepository $articleRepository):void
    {
        foreach ($this->getArticles() as $article){
            $articleRepository->remove($article, true);
        }
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

    public function getCardHeader(): ?string
    {
        return $this->cardHeader;
    }

    public function setCardHeader(?string $cardHeader): self
    {
        $this->cardHeader = $cardHeader;

        return $this;
    }

    public function getCardCompanyName(): ?string
    {
        return $this->cardCompanyName;
    }

    public function setCardCompanyName(?string $cardCompanyName): self
    {
        $this->cardCompanyName = $cardCompanyName;

        return $this;
    }

    public function getCardCompanyPhone(): ?string
    {
        return $this->cardCompanyPhone;
    }

    public function setCardCompanyPhone(?string $cardCompanyPhone): self
    {
        $this->cardCompanyPhone = $cardCompanyPhone;

        return $this;
    }

    public function getCardCompanyEmail(): ?string
    {
        return $this->cardCompanyEmail;
    }

    public function setCardCompanyEmail(?string $cardCompanyEmail): self
    {
        $this->cardCompanyEmail = $cardCompanyEmail;

        return $this;
    }

    public function getCardCompanyWebsite(): ?string
    {
        return $this->cardCompanyWebsite;
    }

    public function setCardCompanyWebsite(?string $cardCompanyWebsite): self
    {
        $this->cardCompanyWebsite = $cardCompanyWebsite;

        return $this;
    }

}
