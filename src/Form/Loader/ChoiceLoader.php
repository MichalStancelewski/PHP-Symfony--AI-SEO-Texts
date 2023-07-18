<?php

namespace App\Form\Loader;


use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface as ChoiceListInterfaceAlias;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Doctrine\ORM\EntityManagerInterface;


class ChoiceLoader implements ChoiceLoaderInterface
{
    private $em;
    private $entityClass;
    private $loaded = false;

    public function __construct(EntityManagerInterface $em, string $entityClass)
    {
        $this->em = $em;
        $this->entityClass = $entityClass;
    }

    public function loadChoiceList($value = null): ChoiceListInterfaceAlias
    {
        $elements = $this->em->getRepository($this->entityClass)->findAll();

        $choices = [];
        foreach ($elements as $element) {
            $choices[$element->getName()] = $element->getId();
        }

        return new ArrayChoiceList($choices);
    }

    public function loadChoicesForValues(array $values, $value = null): array
    {
        $choices = $this->em->getRepository($this->entityClass)->findBy(['id' => $values]);

        $names = [];
        foreach ($choices as $domainGroup) {
            $names[] = $domainGroup->getId();
        }

        return $names;
    }

    public function loadValuesForChoices(array $choices, $value = null): array
    {
        return $choices;
    }

    public function isLoadable(array $values, $value = null): bool
    {
        return !$this->loaded;
    }
}