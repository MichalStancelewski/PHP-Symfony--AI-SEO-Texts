<?php

namespace App\Controller;

use App\Communicator\DatabaseInsert;
use App\Entity\Project;
use App\Entity\Task;
use App\Export\ProjectExporter;
use App\Form\FormRequest;
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

        if ($form->isSubmitted() && $form->isValid()) {
            $formRequest = $form->getData();

            $project = new Project(
                $formRequest->getName(),
                $formRequest->getTheme(),
                $formRequest->getNumberOfArticles(),
                $formRequest->getTextsLength(),
                $formRequest->getWithTitle()
            );

            $entityManager = $this->entityManager;
            $databaseInsert = new DatabaseInsert($entityManager, $project);
            $databaseInsert->saveProject();

            for ($i = 0; $i < $formRequest->getNumberOfArticles(); $i++) {
                $task = new Task();
                $task->setProject($project);
                $task->setName($formRequest->getName());
                $task->setLength($formRequest->getTextsLength());
                $task->setTheme($formRequest->getTheme());
                $task->setWithTitle($formRequest->getWithTitle());
                $task->setStatus("new");
                $task->setDateCreated(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Warsaw')));
                $task->setLastChangedDate(new \DateTime('now', new \DateTimeZone('Europe/Warsaw')));

                $databaseInsert->saveTask($task);
            }

            return $this->render('dashboard/new.html.twig', [
                //TODO redirect with success msg
                'form' => $form,
                'submission' => $formRequest,
            ]);
        }

        return $this->render('dashboard/new.html.twig', [
            //TODO redirect with failure msg
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
        foreach ($project->getArticles() as $article)
        {
            if ($article->isIsUsed()){
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
        $path = $projectExporter->export();

        return $this->render('dashboard/projects-export.html.twig', [
            'project' => $project,
            'path' => $path,
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


}
