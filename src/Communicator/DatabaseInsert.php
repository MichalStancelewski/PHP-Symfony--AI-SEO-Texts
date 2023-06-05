<?php

namespace App\Communicator;

use App\Entity\Article;
use App\Entity\Project;
use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;

class DatabaseInsert
{
    private EntityManagerInterface $entityManager;
    private Project $project;

    public function __construct(EntityManagerInterface $entityManager, Project $project)
    {
        $this->entityManager = $entityManager;
        $this->project = $project;
    }

    public function saveProject(): void
    {
        $entityManager = $this->entityManager;
        $project = $this->project;

        $entityManager->persist($project);
        $entityManager->flush();
    }

    public function saveArticle(Article $article): void
    {
        $entityManager = $this->entityManager;

        $entityManager->persist($article);
        $entityManager->flush();
    }

    public function saveTask(Task $task): void
    {
        $entityManager = $this->entityManager;

        $entityManager->persist($task);
        $entityManager->flush();
    }

    public function editProjectName(string $name): void
    {
        $entityManager = $this->entityManager;
        $project = $this->project;
        $project->setName($name);
        $entityManager->flush();
    }


}