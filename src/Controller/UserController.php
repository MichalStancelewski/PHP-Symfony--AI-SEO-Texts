<?php

namespace App\Controller;

use App\Communicator\DatabaseInsert;
use App\Entity\Article;
use App\Entity\Project;
use App\Entity\Task;
use App\Export\ProjectExporter;
use App\Form\FormEditProject;
use App\Form\FormRequest;
use App\Form\Type\FormEditProjectType;
use App\Form\Type\FormRequestType;
use App\Repository\ArticleRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProjectRepository      $projectRepository,
        private ArticleRepository      $articleRepository
    )
    {
    }


    #[Route('/', name: 'app_user_panel_index')]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $projectRepository = $this->projectRepository;
        $projectsNewest = $projectRepository->findNewest(5);

        return $this->render('dashboard/index.html.twig', [
            'projectsNewest' => $projectsNewest,
        ]);

    }

    #[Route('/new/', name: 'app_user_panel_new')]
    public function new(Request $request): Response
    {
        $formRequest = new FormRequest();

        $form = $this->createForm(FormRequestType::class, $formRequest);
        $form->handleRequest($request);

        $errors = array();
        foreach ($form->getErrors(true, true) as $error) {
            $errors[] = $error->getMessage();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $formRequest = $form->getData();

            $project = new Project(
                $formRequest->getName(),
                $formRequest->getTheme(),
                $formRequest->getNumberOfArticles(),
                $formRequest->getTextsLength(),
                $formRequest->getWithTitle(),
                $formRequest->getLanguage()
            );

            $project->setCardLinkCoverage($formRequest->getCardLinkCoverage());
            if ($formRequest->getCardHeader()) {
                $project->setCardHeader($formRequest->getCardHeader());
            };
            if ($formRequest->getCardCompanyName()) {
                $project->setCardCompanyName($formRequest->getCardCompanyName());
            };
            if ($formRequest->getCardCompanyPhone()) {
                $project->setCardCompanyPhone($formRequest->getCardCompanyPhone());
            };
            if ($formRequest->getCardCompanyEmail()) {
                $project->setCardCompanyEmail($formRequest->getCardCompanyEmail());
            };
            if ($formRequest->getCardCompanyWebsite()) {
                $project->setCardCompanyWebsite($formRequest->getCardCompanyWebsite());
            };

            $entityManager = $this->entityManager;
            $databaseInsert = new DatabaseInsert($entityManager, $project);
            $databaseInsert->saveProject();

            for ($i = 0; $i < $formRequest->getNumberOfArticles(); $i++) {
                $task = new Task();
                $task->setProject($project);
                $task->setLength($formRequest->getTextsLength());
                $task->setTheme($formRequest->getTheme());
                $task->setWithTitle($formRequest->getWithTitle());
                $task->setStatus("new");
                $task->setDateCreated(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Warsaw')));
                $task->setLastChangedDate(new \DateTime('now', new \DateTimeZone('Europe/Warsaw')));
                $task->setLanguage($formRequest->getLanguage());

                $databaseInsert->saveTask($task);
            }

            return $this->render('dashboard/new.html.twig', [
                'isSuccess' => 'success',
                'form' => $form,
                'submission' => $formRequest,
            ]);
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->render('dashboard/new.html.twig', [
                'isSuccess' => 'failure',
                'form' => $form,
                'errors' => $errors,
            ]);
        }

        return $this->render('dashboard/new.html.twig', [
            'isSuccess' => '',
            'form' => $form,
        ]);
    }

    #[Route('/projects/', name: 'app_user_panel_projects')]
    public function projects(Request $request): Response
    {
        $projectRepository = $this->projectRepository;
        $projectsPending = $projectRepository->findAllPending();
        $projectsDone = $projectRepository->findAllDone();

        return $this->render('dashboard/projects.html.twig', [
            'projectsPending' => $projectsPending,
            'projectsDone' => $projectsDone,
            'numberOfPending' => count($projectsPending),
            'numberOfDone' => count($projectsDone),
            'numberTotal' => count($projectsPending) + count($projectsDone),
        ]);
    }

    #[Route('/projects/{id}/', name: 'app_user_panel_projects_single')]
    public function singleProject(Project $project): Response
    {
        $numberOfUsed = 0;
        foreach ($project->getArticles() as $article) {
            if ($article->isIsUsed()) {
                $numberOfUsed++;
            }
        }

        return $this->render('dashboard/projects-single.html.twig', [
            'project' => $project,
            'numberOfUsed' => $numberOfUsed,
        ]);
    }

    #[Route('/projects/{id}/export/', name: 'app_user_panel_projects_export')]
    public function export(Project $project): Response
    {
        $projectExporter = new ProjectExporter($project);
        $pathToPlain = $projectExporter->export(false);
        $pathToAdvanced = $projectExporter->export(true);

        return $this->render('dashboard/projects-export.html.twig', [
            'project' => $project,
            'pathToPlain' => $pathToPlain,
            'pathToAdvanced' => $pathToAdvanced,
        ]);
    }

    #[Route('/projects/{id}/make-used/', name: 'app_user_panel_projects_makeused')]
    public function makeUsed(Project $project): Response
    {
        $articleRepository = $this->articleRepository;
        $project->makeUsed($articleRepository);

        return $this->render('dashboard/projects-makeused.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/projects/{id}/delete/', name: 'app_user_panel_projects_delete')]
    public function delete(Project $project): Response
    {
        return $this->render('dashboard/projects-delete.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/projects/{id}/delete/confirm/', name: 'app_user_panel_projects_delete_confirm')]
    public function deleteConfirmed(Project $project): Response
    {
        $project->deleteArticles($this->articleRepository);
        $this->projectRepository->remove($project, true);
        return $this->redirectToRoute('app_user_panel_projects');
    }

    #[Route('/projects/{id}/edit/', name: 'app_user_panel_projects_edit')]
    public function editProject(int $id, Request $request): Response
    {
        $entityManager = $this->entityManager;

        $formEditProject = new FormEditProject();
        $form = $this->createForm(FormEditProjectType::class, $formEditProject);
        $form->handleRequest($request);

        $project = $entityManager->getRepository(Project::class)->find($id);

        if (!$project) {
            return $this->render('dashboard/projects-edit.html.twig', [
                'isSuccess' => 'failure',
                'form' => $form,
                'errors' => 'Nie istnieje projekt o ID: ' . $id,
            ]);
        }

        $errors = array();
        foreach ($form->getErrors(true, true) as $error) {
            $errors[] = $error->getMessage();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $formRequest = $form->getData();

            $project->setCardLinkCoverage($formRequest->getCardLinkCoverage());
            if ($formRequest->getCardHeader()) {
                $project->setCardHeader($formRequest->getCardHeader());
            };
            if ($formRequest->getCardCompanyName()) {
                $project->setCardCompanyName($formRequest->getCardCompanyName());
            };
            if ($formRequest->getCardCompanyPhone()) {
                $project->setCardCompanyPhone($formRequest->getCardCompanyPhone());
            };
            if ($formRequest->getCardCompanyEmail()) {
                $project->setCardCompanyEmail($formRequest->getCardCompanyEmail());
            };
            if ($formRequest->getCardCompanyWebsite()) {
                $project->setCardCompanyWebsite($formRequest->getCardCompanyWebsite());
            };

            $databaseInsert = new DatabaseInsert($entityManager, $project);
            $databaseInsert->editProjectName($formRequest->getName());

            return $this->render('dashboard/projects-edit.html.twig', [
                'isSuccess' => 'success',
                'project' => $project,
                'form' => $form,
                'submission' => $formRequest,
            ]);
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->render('dashboard/projects-edit.html.twig', [
                'isSuccess' => 'failure',
                'project' => $project,
                'form' => $form,
                'errors' => $errors,
            ]);
        }

        return $this->render('dashboard/projects-edit.html.twig', [
            'isSuccess' => '',
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/projects/{projectId}/regenerate/{articleId}/', name: 'app_user_panel_projects_regenerate_article')]
    public function regenerateArticle(int $projectId, int $articleId, Request $request): Response
    {
        $entityManager = $this->entityManager;
        $articleRepository = $this->articleRepository;

        $project = $entityManager->getRepository(Project::class)->find($projectId);
        $article = $entityManager->getRepository(Article::class)->find($articleId);

        if ($article && $project) {

            try {
                $articleRepository->remove($article, true);
            } catch (\Exception $e) {
                return $this->render('dashboard/ajax/regenerate-article.html.twig', [
                    'isSuccess' => false,
                    'error' => $e->getMessage(),
                ]);
            }
            $databaseInsert = new DatabaseInsert($entityManager, $project);

            $task = new Task();
            $task->setProject($project);
            $task->setLength($project->getTextsLength());
            $task->setTheme($project->getTheme());
            $task->setWithTitle($project->isWithTitle());
            $task->setStatus("new");
            $task->setDateCreated(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Warsaw')));
            $task->setLastChangedDate(new \DateTime('now', new \DateTimeZone('Europe/Warsaw')));
            $task->setLanguage($project->getLanguage());

            try {
                $databaseInsert->saveTask($task);
            } catch (\Exception $e) {
                return $this->render('dashboard/ajax/regenerate-article.html.twig', [
                    'isSuccess' => false,
                    'message' => $e->getMessage(),
                ]);
            }

            return $this->render('dashboard/ajax/regenerate-article.html.twig', [
                'isSuccess' => true,
                'message' => 'Wysłano żądanie',
            ]);
        }

        return $this->render('dashboard/ajax/regenerate-article.html.twig', [
            'isSuccess' => false,
            'message' => 'Wystąpił błąd!',
        ]);

    }

    #[Route('/projects/switch/{articleId}/{toggleValue}', name: 'app_user_panel_projects_switch_is_used_article')]
    public function switchIsUsed(int $articleId, bool $toggleValue, Request $request): Response
    {
        $entityManager = $this->entityManager;
        $articleRepository = $this->articleRepository;

        $article = $entityManager->getRepository(Article::class)->find($articleId);

        if ($article) {
            try {
                $articleRepository->setIsUsed($article, $toggleValue);
            }
            catch (\Exception $e) {
                    return $this->render('dashboard/ajax/switch-is-used-article.html.twig', [
                        'isSuccess' => false,
                        'message' => $e->getMessage(),
                    ]);
                }
            return $this->render('dashboard/ajax/switch-is-used-article.html.twig', [
                'isSuccess' => true,
                'message' => 'Wysłano żądanie!',
            ]);
        }

        return $this->render('dashboard/ajax/switch-is-used-article.html.twig', [
            'isSuccess' => false,
            'message' => 'Wystąpił błąd!',
        ]);
    }
}
