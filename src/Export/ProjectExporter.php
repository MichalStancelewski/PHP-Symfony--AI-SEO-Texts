<?php

namespace App\Export;

use App\Entity\Project;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class ProjectExporter
{
    private Project $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function export(bool $isAdvanced): string
    {
        $project = $this->project;
        $date = new \DateTime('now', new \DateTimeZone('Europe/Warsaw'));
        $slug = strtolower($this->cleanString($project->getName()));

        if ($isAdvanced) {
            $url = '/uploads/export/' . $date->format('Y-m-d_H-i') . '_' . $slug . '-advanced.txt';
        } else {
            $url = '/uploads/export/' . $date->format('Y-m-d_H-i') . '_' . $slug . '.txt';
        }


        $path = getcwd() . $url;

        try {
            $file = new Filesystem();
            $file->dumpFile($path, '');

        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while creating new file at " . $exception->getPath();
        }

        $iterator = 0;

        foreach ($project->getArticles() as $article) {
            $iterator++;

            try {
                if ($isAdvanced) {
                    $file->appendToFile($path, 'TITLE: ' . $article->getTitle() . "\n");
                    $file->appendToFile($path, 'POST TYPE: post' . "\n");
                    $file->appendToFile($path, 'AUTHOR:' . "\n");
                    $file->appendToFile($path, 'TAGS:' . "\n");
                    $file->appendToFile($path, 'SLUG:' . "\n");
                    $file->appendToFile($path, 'EXCERPT:' . "\n");
                    $file->appendToFile($path, 'BODY: ' . $article->getContent() . "\n");
                    $file->appendToFile($path, 'ALLOW COMMENTS: 0' . "\n");
                    $file->appendToFile($path, 'ALLOW PINGBACKS: 0' . "\n");
                    $file->appendToFile($path, 'SITES: ' . "\n");

                    if ($iterator != count($project->getArticles())) {
                        $file->appendToFile($path, "\n");
                        $file->appendToFile($path, '[NEW]' . "\n");
                        $file->appendToFile($path, "\n");
                    }
                } else {
                    $file->appendToFile($path, $article->getTitle() . "\n");
                    $file->appendToFile($path, $article->getContent() . "\n");
                    $file->appendToFile($path, "\n");
                }

            } catch (IOExceptionInterface $exception) {
                echo "An error occurred while appending new content at " . $exception->getPath() . ' with ' . $article->getTitle();
            }
        }

        return $url;

    }

    private function cleanString($string): string
    {
        $string = str_replace(' ', '-', $string);
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
        return preg_replace('/-+/', '-', $string);
    }

}