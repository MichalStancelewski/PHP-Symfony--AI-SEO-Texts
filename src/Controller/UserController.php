<?php

namespace App\Controller;

use App\Communicator\DatabaseInsert;
use App\Entity\Project;
use App\Entity\Task;
use App\Form\FormRequest;
use App\Form\Type\FormRequestType;
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
        private ProjectRepository $projectRepository
    )
    {
    }


    #[Route('/', name: 'app_user_panel_index')]
    public function index(Request $request): Response
    {
        return $this->render('dashboard/index.html.twig', [
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
    public function singleProject(Project $project, ProjectRepository $technologyRepository): Response
    {

        return $this->render('dashboard/projects-single.html.twig', [
            'project' => $project,
        ]);
    }


}
