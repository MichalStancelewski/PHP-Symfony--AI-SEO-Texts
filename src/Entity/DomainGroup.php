<?php

namespace App\Entity;

use App\Repository\DomainGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DomainGroupRepository::class)]
class DomainGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 512)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'domainGroup', targetEntity: Domain::class,  cascade: ['persist'],  orphanRemoval: true)]
    private Collection $domains;

    #[ORM\OneToMany(mappedBy: 'domainGroup', targetEntity: ProjectGroup::class)]
    private Collection $projectGroups;

    public function __construct(string $name)
    {
        $this->domains = new ArrayCollection();
        $this->name = $name;
        $this->projectGroups = new ArrayCollection();
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

    /**
     * @return Collection<int, Domain>
     */
    public function getDomains(): Collection
    {
        return $this->domains;
    }

    public function addDomain(Domain $domain): self
    {
        if (!$this->domains->contains($domain)) {
            $this->domains->add($domain);
            $domain->setDomainGroup($this);
        }

        return $this;
    }

    public function removeDomain(Domain $domain): self
    {
        if ($this->domains->removeElement($domain)) {
            // set the owning side to null (unless already changed)
            if ($domain->getDomainGroup() === $this) {
                $domain->setDomainGroup(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return Collection<int, ProjectGroup>
     */
    public function getProjectGroups(): Collection
    {
        return $this->projectGroups;
    }

    public function addProjectGroup(ProjectGroup $projectGroup): self
    {
        if (!$this->projectGroups->contains($projectGroup)) {
            $this->projectGroups->add($projectGroup);
            $projectGroup->setDomainGroup($this);
        }

        return $this;
    }

    public function removeProjectGroup(ProjectGroup $projectGroup): self
    {
        if ($this->projectGroups->removeElement($projectGroup)) {
            // set the owning side to null (unless already changed)
            if ($projectGroup->getDomainGroup() === $this) {
                $projectGroup->setDomainGroup(null);
            }
        }

        return $this;
    }

}
