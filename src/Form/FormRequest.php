<?php

namespace App\Form;
use Symfony\Component\Validator\Constraints as Assert;

class FormRequest
{
    #[Assert\NotBlank
    (
        message: 'Liczba artykułów - Pole nie może byc puste.',
    )]
    #[Assert\Range(
        min: 1,
        max: 100,
        notInRangeMessage: 'Liczba artykułów - Podaj wartość między {{ min }} a {{ max }}.',
    )]
    private ?int $numberOfArticles = null;

    private ?bool $withTitle = null;

    #[Assert\NotBlank
    (
        message: 'Temat - Pole nie może byc puste.',
    )]
    #[Assert\Length(
        min: 10,
        max: 240,
        minMessage: 'Temat - Pole musi mieć minimum {{ limit }} znaków.',
        maxMessage: 'Temat - Pole musi mieć maksymalnie {{ limit }} znaków.',
    )]
    private ?string $theme = null;

    #[Assert\NotBlank
    (
        message: 'Długość tekstu - Pole nie może byc puste.',
    )]
    #[Assert\Range(
        min: 200,
        max: 5000,
        notInRangeMessage: 'Długość tekstu - Podaj wartość między {{ min }} a {{ max }}.',
    )]
    private ?int $textsLength = null;

    #[Assert\Length(
        min: 5,
        max: 80,
        minMessage: 'Nazwa - Pole musi mieć minimum {{ limit }} znaków.',
        maxMessage: 'Nazwa - Pole musi mieć maksymalnie {{ limit }} znaków.',
    )]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[Assert\Length(
        min: 3,
        max: 3,
        minMessage: 'Język - Pole w nieprawidłowym formacie.',
        maxMessage: 'Język - Pole w nieprawidłowym formacie.',
    )]
    #[Assert\NotBlank]
    private ?string $language = null;

    /**
     * @return int|null
     */
    public function getNumberOfArticles(): ?int
    {
        return $this->numberOfArticles;
    }

    /**
     * @param int|null $numberOfArticles
     */
    public function setNumberOfArticles(?int $numberOfArticles): void
    {
        $this->numberOfArticles = $numberOfArticles;
    }

    /**
     * @return bool|null
     */
    public function getWithTitle(): ?bool
    {
        return $this->withTitle;
    }

    /**
     * @param bool|null $withTitle
     */
    public function setWithTitle(?bool $withTitle): void
    {
        $this->withTitle = $withTitle;
    }

    /**
     * @return string|null
     */
    public function getTheme(): ?string
    {
        return $this->theme;
    }

    /**
     * @param string|null $theme
     */
    public function setTheme(?string $theme): void
    {
        $this->theme = $theme;
    }

    /**
     * @return int|null
     */
    public function getTextsLength(): ?int
    {
        return $this->textsLength;
    }

    /**
     * @param int|null $textsLength
     */
    public function setTextsLength(?int $textsLength): void
    {
        $this->textsLength = $textsLength;
    }

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
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * @param string|null $language
     */
    public function setLanguage(?string $language): void
    {
        $this->language = strtoupper($language);
    }

}