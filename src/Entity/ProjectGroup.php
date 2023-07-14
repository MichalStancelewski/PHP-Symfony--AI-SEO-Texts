<?php

namespace App\Entity;

use App\Repository\ProjectGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectGroupRepository::class)]
class ProjectGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 512)]
    private ?string $name = null;

    #[ORM\OneToOne(inversedBy: 'projectGroup', cascade: ['persist', 'remove'])]
    private ?DomainGroup $domainGroup = null;

    #[ORM\OneToMany(mappedBy: 'projectGroup', targetEntity: Project::class)]
    private Collection $projects;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
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

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->setProjectGroup($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getProjectGroup() === $this) {
                $project->setProjectGroup(null);
            }
        }

        return $this;
    }
}
