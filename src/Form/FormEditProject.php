<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class FormEditProject
{

    #[Assert\Length(
        min: 5,
        max: 80,
        minMessage: 'Nazwa - Pole musi mieć minimum {{ limit }} znaków.',
        maxMessage: 'Nazwa - Pole musi mieć maksymalnie {{ limit }} znaków.',
    )]
    #[Assert\NotBlank]
    private ?string $name = null;

    private ?string $cardHeader = null;

    private ?string $cardCompanyName = null;

    private ?string $cardCompanyPhone = null;

    private ?string $cardCompanyEmail = null;

    private ?string $cardCompanyWebsite = null;

    private ?int $cardLinkCoverage = null;

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
    public function getCardHeader(): ?string
    {
        return $this->cardHeader;
    }

    /**
     * @param string|null $cardHeader
     */
    public function setCardHeader(?string $cardHeader): void
    {
        $this->cardHeader = $cardHeader;
    }

    /**
     * @return string|null
     */
    public function getCardCompanyName(): ?string
    {
        return $this->cardCompanyName;
    }

    /**
     * @param string|null $cardCompanyName
     */
    public function setCardCompanyName(?string $cardCompanyName): void
    {
        $this->cardCompanyName = $cardCompanyName;
    }

    /**
     * @return string|null
     */
    public function getCardCompanyPhone(): ?string
    {
        return $this->cardCompanyPhone;
    }

    /**
     * @param string|null $cardCompanyPhone
     */
    public function setCardCompanyPhone(?string $cardCompanyPhone): void
    {
        $this->cardCompanyPhone = $cardCompanyPhone;
    }

    /**
     * @return string|null
     */
    public function getCardCompanyEmail(): ?string
    {
        return $this->cardCompanyEmail;
    }

    /**
     * @param string|null $cardCompanyEmail
     */
    public function setCardCompanyEmail(?string $cardCompanyEmail): void
    {
        $this->cardCompanyEmail = $cardCompanyEmail;
    }

    /**
     * @return string|null
     */
    public function getCardCompanyWebsite(): ?string
    {
        return $this->cardCompanyWebsite;
    }

    /**
     * @param string|null $cardCompanyWebsite
     */
    public function setCardCompanyWebsite(?string $cardCompanyWebsite): void
    {
        $this->cardCompanyWebsite = $cardCompanyWebsite;
    }

    /**
     * @return int|null
     */
    public function getCardLinkCoverage(): ?int
    {
        return $this->cardLinkCoverage;
    }

    /**
     * @param int|null $cardLinkCoverage
     */
    public function setCardLinkCoverage(?int $cardLinkCoverage): void
    {
        $this->cardLinkCoverage = $cardLinkCoverage;
    }


}