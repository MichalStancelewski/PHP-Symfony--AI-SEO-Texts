<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class FormEditProjectGroup
{
    #[Assert\Length(
        min: 5,
        max: 320,
        minMessage: 'Nazwa - Pole musi mieć minimum {{ limit }} znaków.',
        maxMessage: 'Nazwa - Pole musi mieć maksymalnie {{ limit }} znaków.',
    )]
    #[Assert\NotBlank]
    private ?string $name = null;

    private ?array $projects;

    private ?int $domainGroup = null;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return array|null
     */
    public function getProjects(): ?array
    {
        return $this->projects;
    }

    /**
     * @param array|null $projects
     */
    public function setProjects(?array $projects): void
    {
        $this->projects = $projects;
    }

    /**
     * @return int|null
     */
    public function getDomainGroup(): ?int
    {
        return $this->domainGroup;
    }

    /**
     * @param int|null $domainGroup
     */
    public function setDomainGroup(?int $domainGroup): void
    {
        $this->domainGroup = $domainGroup;
    }

}