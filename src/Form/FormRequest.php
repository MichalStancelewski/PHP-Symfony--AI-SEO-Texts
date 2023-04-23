<?php

namespace App\Form;

class FormRequest
{
    private ?int $numberOfArticles = null;

    private ?bool $withTitle = null;

    private ?string $theme = null;

    private ?int $textsLength = null;

    private ?string $name = null;

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

}