<?php

namespace App\Export;

use App\Entity\Project;
use App\Entity\ProjectGroup;
use App\Repository\DomainRepository;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class ProjectGroupExporter
{
    private ProjectGroup $projectGroup;
    private DomainRepository $domainRepository;

    public function __construct(ProjectGroup $projectGroup, $domainRepository)
    {
        $this->projectGroup = $projectGroup;
        $this->domainRepository = $domainRepository;
    }

    public function export(bool $isAdvanced): string
    {
        $projectGroup = $this->projectGroup;
        $date = new \DateTime('now', new \DateTimeZone('Europe/Warsaw'));
        $slug = strtolower($this->cleanString($projectGroup->getName()));

        if ($isAdvanced) {
            $url = '/uploads/export/' . $date->format('Y-m-d_H-i') . '_' . $slug . '-advanced.txt';
        } else {
            $url = '/uploads/export/' . $date->format('Y-m-d_H-i') . '_' . $slug . '.txt';
        }

        $sites = null;
        if ($projectGroup->getDomainGroup()) {
            $domainsArray = $this->domainRepository->findByGroupId($projectGroup->getDomainGroup());
            $urlArray = [];
            foreach ($domainsArray as $domain) {
                $urlArray[] = $domain->getName();
            }
            $sites = $urlArray;
        } else {
            $sites = [''];
        }
  //  dd($sites);
        $path = getcwd() . $url;

        try {
            $file = new Filesystem();
            $file->dumpFile($path, '');

        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while creating new file at " . $exception->getPath();
        }

        $iterator = 0;
        $sitesIterator = 0;
        $articlesWithLink = [];

        foreach ($projectGroup->getProjects() as $project) {
            $linkCoverage = $project->getCardLinkCoverage();

            if ($linkCoverage > 0) {
                $linkCoverage = ($linkCoverage / 100) * $project->getNumberOfArticles();
                $linkCoverage = intval(round($linkCoverage, 0, PHP_ROUND_HALF_UP));
                while (sizeof($articlesWithLink) < $linkCoverage) {
                    $rng = rand(1, $project->getNumberOfArticles());
                    if (array_search($rng, $articlesWithLink) === false) {
                        $articlesWithLink[] = $rng;
                    }
                }
            }

            foreach ($project->getArticles() as $article) {
                $iterator++;

                if (array_search($iterator, $articlesWithLink) !== false) {
                    $body = $this->appendCardToArticle($article->getContent(), $project);
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
                        $file->appendToFile($path, 'BODY: ' . $this->removeBlockquoteTag($body) . "\n");
                        $file->appendToFile($path, 'ALLOW COMMENTS: 0' . "\n");
                        $file->appendToFile($path, 'ALLOW PINGBACKS: 0' . "\n");
                        $file->appendToFile($path, 'SITES: ' . $sites[$sitesIterator++] . "\n");

                        $file->appendToFile($path, "\n");
                        $file->appendToFile($path, '[NEW]' . "\n");
                        $file->appendToFile($path, "\n");

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

                if(sizeof($sites) < $this->countArticlesInGroup() && $sitesIterator == sizeof($sites)){
                    $sitesIterator = 0;
                }
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

    private function appendCardToArticle(string $article, Project $project): string
    {
        $cardHeader = $project->getCardHeader();
        $cardCompanyName = $project->getCardCompanyName();
        $cardCompanyPhone = $project->getCardCompanyPhone();
        $cardCompanyEmail = $project->getCardCompanyEmail();
        $cardCompanyWebsite = $project->getCardCompanyWebsite();

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

    function removeBlockquoteTag(string $text): string
    {
        $pattern = '/<blockquote\b[^>]*>(.*?)<\/blockquote>/is';

        $filtered_text = preg_replace($pattern, '', $text);

        return $filtered_text;
    }

    private function countArticlesInGroup(): int
    {
        $count = 0;
        foreach ($this->projectGroup->getProjects() as $project){
            $count += sizeOf($project->getArticles());
        }
        return $count;
    }

}