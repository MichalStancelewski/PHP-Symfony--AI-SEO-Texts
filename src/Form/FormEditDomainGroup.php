<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class FormEditDomainGroup
{
    #[Assert\Length(
        min: 5,
        max: 320,
        minMessage: 'Nazwa - Pole musi mieć minimum {{ limit }} znaków.',
        maxMessage: 'Nazwa - Pole musi mieć maksymalnie {{ limit }} znaków.',
    )]
    #[Assert\NotBlank]
    private ?string $name = null;

    private ?string $domainsList = null;

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
     * @return string|null
     */
    public function getDomainsList(): ?string
    {
        return $this->domainsList;
    }

    /**
     * @param string|null $domainsList
     */
    public function setDomainsList(?string $domainsList): void
    {
        $this->domainsList = $domainsList;
    }

}