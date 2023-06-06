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
        $articlesWithLink = [];
        $linkCoverage = $this->project->getCardLinkCoverage();
        if ($linkCoverage > 0) {
            $linkCoverage = ($linkCoverage / 100) * $this->project->getNumberOfArticles();
            $linkCoverage = intval(round($linkCoverage, 0, PHP_ROUND_HALF_UP));
            while (sizeof($articlesWithLink) < $linkCoverage) {
                $rng = rand(1, $this->project->getNumberOfArticles());
                if (!array_search($rng, $articlesWithLink)) {
                    $articlesWithLink[] = $rng;
                }
            }
        }

        foreach ($project->getArticles() as $article) {
            $iterator++;

            if (array_search($iterator, $articlesWithLink)) {
                $body = $this->appendCardToArticle($article->getContent());
            } else {
                $body = $article->getContent();
            }

            try {
                if ($isAdvanced) {
                    $file->appendToFile($path, 'TITLE: ' . $article->getTitle() . "\n");
                    $file->appendToFile($path, 'POST TYPE: post' . "\n");
                    $file->appendToFile($path, 'AUTHOR:' . "\n");
                    $file->appendToFile($path, 'TAGS:' . "\n");
                    $file->appendToFile($path, 'SLUG:' . "\n");
                    $file->appendToFile($path, 'EXCERPT:' . "\n");
                    $file->appendToFile($path, 'BODY: ' . $body . "\n");
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
                    $file->appendToFile($path, $body . "\n");
                    $file->appendToFile($path, "\n");
                    $file->appendToFile($path, "##################################################");
                    $file->appendToFile($path, "\n");
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

    private function appendCardToArticle(string $article): string
    {
        $cardHeader = $this->project->getCardHeader();
        $cardCompanyName = $this->project->getCardCompanyName();
        $cardCompanyPhone = $this->project->getCardCompanyPhone();
        $cardCompanyEmail = $this->project->getCardCompanyEmail();
        $cardCompanyWebsite = $this->project->getCardCompanyWebsite();

        return $article . "\n\n" .
            '<h3>' . $cardHeader . '</h3>' .
            '<b>' . $cardCompanyName . '</b>' .
            '<ul style="list-style: none;">' .
            '<li><span class="dashicons-before dashicons-phone">' . $cardCompanyPhone . '</span></li>' .
            '<li><span class="dashicons-before dashicons-email">' . $cardCompanyEmail . '</span></li>' .
            '<li><span class="dashicons-before dashicons-desktop">' .
            '<u><a href="' . $cardCompanyWebsite . '" target="_blank" rel="noopener">' . $cardCompanyWebsite . '</a></u>' .
            '</span></li>' .
            '</ul>';
    }

}