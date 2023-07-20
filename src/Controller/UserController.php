<?php

namespace App\Controller;

use App\Communicator\DatabaseInsert;
use App\Entity\Article;
use App\Entity\Domain;
use App\Entity\DomainGroup;
use App\Entity\Project;
use App\Entity\ProjectGroup;
use App\Entity\Task;
use App\Export\ProjectExporter;
use App\Form\FormEditDomainGroup;
use App\Form\FormEditProject;
use App\Form\FormEditProjectGroup;
use App\Form\FormNewDomainGroup;
use App\Form\FormNewProject;
use App\Form\FormNewProjectGroup;
use App\Form\Type\FormEditDomainGroupType;
use App\Form\Type\FormEditProjectGroupType;
use App\Form\Type\FormEditProjectType;
use App\Form\Type\FormNewDomainGroupType;
use App\Form\Type\FormNewProjectGroupType;
use App\Form\Type\FormNewProjectType;
use App\Repository\ArticleRepository;
use App\Repository\DomainGroupRepository;
use App\Repository\ProjectGroupRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        private LoggerInterface        $logger,
        private EntityManagerInterface $entityManager,
        private ProjectRepository      $projectRepository,
        private ArticleRepository      $articleRepository,
        private DomainGroupRepository  $domainGroupRepository,
        private ProjectGroupRepository $projectGroupRepository
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
    public function newProject(Request $request): Response
    {
        $formRequest = new FormNewProject();

        $form = $this->createForm(FormNewProjectType::class, $formRequest);
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

            return $this->render('dashboard/projects/new.html.twig', [
                'isSuccess' => 'success',
                'form' => $form,
                'submission' => $formRequest,
            ]);
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->render('dashboard/projects/new.html.twig', [
                'isSuccess' => 'failure',
                'form' => $form,
                'errors' => $errors,
            ]);
        }

        return $this->render('dashboard/projects/new.html.twig', [
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

        return $this->render('dashboard/projects/all.html.twig', [
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

        return $this->render('dashboard/projects/single.html.twig', [
            'project' => $project,
            'numberOfUsed' => $numberOfUsed,
        ]);
    }

    #[Route('/projects/{id}/export/', name: 'app_user_panel_projects_export')]
    public function exportProject(Project $project): Response
    {
        $projectExporter = new ProjectExporter($project);
        $pathToPlain = $projectExporter->export(false);
        $pathToAdvanced = $projectExporter->export(true);

        return $this->render('dashboard/projects/export.html.twig', [
            'project' => $project,
            'pathToPlain' => $pathToPlain,
            'pathToAdvanced' => $pathToAdvanced,
        ]);
    }

    #[Route('/projects/{id}/make-used/', name: 'app_user_panel_projects_makeused')]
    public function makeUsedProject(Project $project): Response
    {
        $articleRepository = $this->articleRepository;
        $project->makeUsed($articleRepository);

        return $this->render('dashboard/projects/makeused.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/projects/{id}/delete/', name: 'app_user_panel_projects_delete')]
    public function deleteProject(Project $project): Response
    {
        return $this->render('dashboard/projects/delete.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/projects/{id}/delete/confirm/', name: 'app_user_panel_projects_delete_confirm')]
    public function deleteProjectConfirmed(Project $project): Response
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
            return $this->render('dashboard/projects/edit.html.twig', [
                'isSuccess' => 'failure',
                'project' => $project,
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

            return $this->render('dashboard/projects/edit.html.twig', [
                'isSuccess' => 'success',
                'project' => $project,
                'form' => $form,
                'submission' => $formRequest,
            ]);
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->render('dashboard/projects/edit.html.twig', [
                'isSuccess' => 'failure',
                'project' => $project,
                'form' => $form,
                'errors' => $errors,
            ]);
        }

        return $this->render('dashboard/projects/edit.html.twig', [
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
        $projectRepository = $this->projectRepository;

        $project = $entityManager->getRepository(Project::class)->find($projectId);
        $article = $entityManager->getRepository(Article::class)->find($articleId);

        if ($article && $project) {

            try {
                $articleRepository->remove($article, true);
                $projectRepository->setStatusPending($project, true);
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
            } catch (\Exception $e) {
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

    #[Route('/domains/new/', name: 'app_user_panel_new_domain_group')]
    public function newDomainGroup(Request $request): Response
    {
        $domainsList = null;
        $formRequest = new FormNewDomainGroup();

        $form = $this->createForm(FormNewDomainGroupType::class, $formRequest);
        $form->handleRequest($request);

        $errors = array();
        foreach ($form->getErrors(true, true) as $error) {
            $errors[] = $error->getMessage();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $formRequest = $form->getData();

            $domainGroup = new DomainGroup(
                $formRequest->getName()
            );

            if ($formRequest->getDomainsList()) {
                $domainsList = explode("\n", $formRequest->getDomainsList());
                foreach ($domainsList as $d) {
                    $this->logger->debug('Domain manager | Domain: ' . $d);
                    $domain = new Domain($d);
                    $domainGroup->addDomain($domain);
                }
            }

            $domainGroupRepository = $this->domainGroupRepository;
            $domainGroupRepository->save($domainGroup, true);

            if (is_countable($domainsList)) {
                $domainsNumber = count($domainsList);
            } else {
                $domainsNumber = 0;
            }

            return $this->render('dashboard/domains/new.html.twig', [
                'isSuccess' => 'success',
                'form' => $form,
                'submission' => $formRequest,
                'domainsNumber' => $domainsNumber,
            ]);

        }
        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->render('dashboard/domains/new.html.twig', [
                'isSuccess' => 'failure',
                'form' => $form,
                'errors' => $errors,
            ]);
        }

        return $this->render('dashboard/domains/new.html.twig', [
            'isSuccess' => '',
            'form' => $form,
        ]);
    }

    #[Route('/domains/{id}/edit/', name: 'app_user_panel_domain_group_edit')]
    public function editDomainGroup(int $id, Request $request): Response
    {
        $entityManager = $this->entityManager;

        $formEditDomainGroup = new FormEditDomainGroup();
        $form = $this->createForm(FormEditDomainGroupType::class, $formEditDomainGroup);
        $form->handleRequest($request);

        $domainsList = '';
        $domainGroup = $entityManager->getRepository(DomainGroup::class)->find($id);

        $oldDomains = $domainGroup->getDomains()->toArray();
        $newDomains = [];
        if ($oldDomains) {
            $domainsList = implode("\n", $oldDomains);
        }

        if (!$domainGroup) {
            return $this->render('dashboard/domains/edit.html.twig', [
                'isSuccess' => 'failure',
                'domainsList' => $domainsList,
                'domainGroup' => $domainGroup,
                'form' => $form,
                'errors' => 'Nie istnieje grupa domen o ID: ' . $id,
            ]);
        }

        $errors = array();
        foreach ($form->getErrors(true, true) as $error) {
            $errors[] = $error->getMessage();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $formRequest = $form->getData();

            if ($formRequest->getDomainsList()) {
                $domainsList = explode("\n", $formRequest->getDomainsList());
                foreach ($domainsList as $d) {
                    $domain = new Domain($d);
                    $newDomains[] = $domain;
                }
                $domainsList = implode("\n", $domainsList);
            }

            $domainGroupRepository = $this->domainGroupRepository;
            $domainGroupRepository->edit($domainGroup, $formRequest->getName(), $oldDomains, $newDomains);

            return $this->render('dashboard/domains/edit.html.twig', [
                'isSuccess' => 'success',
                'domainsList' => $domainsList,
                'domainGroup' => $domainGroup,
                'form' => $form,
                'submission' => $formRequest,
            ]);
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->render('dashboard/domains/edit.html.twig', [
                'isSuccess' => 'failure',
                'domainsList' => $domainsList,
                'domainGroup' => $domainGroup,
                'form' => $form,
                'errors' => $errors,
            ]);
        }

        return $this->render('dashboard/domains/edit.html.twig', [
            'isSuccess' => '',
            'domainsList' => $domainsList,
            'domainGroup' => $domainGroup,
            'form' => $form,
        ]);
    }

    #[Route('/domains/', name: 'app_user_panel_domain_group')]
    public function domains(Request $request): Response
    {
        $numberTotalGroups = 0;
        $numberTotalDomains = 0;

        $domainGroupRepository = $this->domainGroupRepository;
        $domainGroups = $domainGroupRepository->findAll();

        if ($domainGroups) {
            $numberTotalGroups = sizeof($domainGroups);

            foreach ($domainGroups as $domainGroup) {
                $numberTotalDomains += sizeof($domainGroup->getDomains());
            }
        }

        return $this->render('dashboard/domains/all.html.twig', [
            'domainGroups' => $domainGroups,
            'numberTotalGroups' => $numberTotalGroups,
            'numberTotalDomains' => $numberTotalDomains,
        ]);
    }

    #[Route('/domains/{id}/', name: 'app_user_panel_domain_group_single')]
    public function singleDomainGroup(DomainGroup $domainGroup): Response
    {
        return $this->render('dashboard/domains/single.html.twig', [
            'domainGroup' => $domainGroup,
        ]);

    }

    #[Route('/domains/{id}/delete/', name: 'app_user_panel_domain_group_delete')]
    public function deleteDomainGroup(DomainGroup $domainGroup): Response
    {
        return $this->render('dashboard/domains/delete.html.twig', [
            'domainGroup' => $domainGroup,
        ]);
    }

    #[Route('/domains/{id}/delete/confirm/', name: 'app_user_panel_domain_group_delete_confirm')]
    public function deleteDomainGroupConfirmed(DomainGroup $domainGroup): Response
    {
        $this->domainGroupRepository->remove($domainGroup, true);
        return $this->redirectToRoute('app_user_panel_domain_group');
    }

    #[Route('/project-groups/new/', name: 'app_user_panel_new_project_group')]
    public function newProjectGroup(Request $request): Response
    {

        $formRequest = new FormNewProjectGroup();

        $form = $this->createForm(FormNewProjectGroupType::class, $formRequest);
        $form->handleRequest($request);

        $errors = array();
        foreach ($form->getErrors(true, true) as $error) {
            $errors[] = $error->getMessage();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $formRequest = $form->getData();

            $projectGroup = new ProjectGroup(
                $formRequest->getName()
            );

            if ($formRequest->getDomainGroup()) {
                $domainGroupRepository = $this->domainGroupRepository;
                $projectGroup->setDomainGroup($domainGroupRepository->find($formRequest->getDomainGroup()));
            }

            if ($formRequest->getProjects()) {
                $projectRepository = $this->projectRepository;
                foreach ($formRequest->getProjects() as $p) {
                    $project = $projectRepository->find($p);
                    $projectGroup->addProject($project);
                }
            }
            $projectGroupRepository = $this->projectGroupRepository;
            $projectGroupRepository->save($projectGroup, true);

            return $this->render('dashboard/project-groups/new.html.twig', [
                'isSuccess' => 'success',
                'form' => $form,
                'submission' => $formRequest
            ]);

        }
        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->render('dashboard/project-groups/new.html.twig', [
                'isSuccess' => 'failure',
                'form' => $form,
                'errors' => $errors,
            ]);
        }

        return $this->render('dashboard/project-groups/new.html.twig', [
            'isSuccess' => '',
            'form' => $form,
        ]);

    }

    #[Route('/project-groups/{id}/edit/', name: 'app_user_panel_project_group_edit')]
    public function editProjectGroup(int $id, Request $request): Response
    {

        $entityManager = $this->entityManager;
        $domainGroupRepository = $this->domainGroupRepository;
        $projectGroupRepository = $this->projectGroupRepository;
        $projectRepository = $this->projectRepository;

        $formEditProjectGroup = new FormEditProjectGroup();
        $form = $this->createForm(FormEditProjectGroupType::class, $formEditProjectGroup);
        $form->handleRequest($request);

        $projectGroup = $entityManager->getRepository(ProjectGroup::class)->find($id);

        if (!$projectGroup) {
            return $this->render('dashboard/project-groups/edit.html.twig', [
                'isSuccess' => 'failure',
                'form' => $form,
                'projectGroup' => $projectGroup,
                'projectsArray' => $projectsArray,
                'errors' => 'Nie istnieje grupa projektów o ID: ' . $id,
            ]);
        }

        $errors = array();
        foreach ($form->getErrors(true, true) as $error) {
            $errors[] = $error->getMessage();
        }

        $projectsArray = [];
        $oldProjectsArray = [];
        $newProjectsArray = [];

        if ($projectGroup->getProjects()) {
            foreach ($projectGroup->getProjects() as $p) {
                $oldProjectsArray[] = $projectRepository->find($p->getId());
                $projectsArray[] = $p->getId();
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $formRequest = $form->getData();

            if ($formRequest->getProjects()) {
                $projectsList = $formRequest->getProjects();
                foreach ($projectsList as $p) {
                    $newProjectsArray[] = $projectRepository->find($p);
                    $this->logger->debug('Projects debug: ' . $p);
                }
            }

            if ($formRequest->getDomainGroup()) {
                $domainGroup = $domainGroupRepository->find($formRequest->getDomainGroup());
            } else {
                $domainGroup = null;
            }

            $projectGroupRepository->edit($projectGroup, $formRequest->getName(), $domainGroup, $oldProjectsArray, $newProjectsArray);

            return $this->redirect($this->generateUrl('app_user_panel_project_group_edit', ['id' => $projectGroup->getId()]));
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->render('dashboard/project-groups/edit.html.twig', [
                'isSuccess' => 'failure',
                'form' => $form,
                'projectGroup' => $projectGroup,
                'projectsArray' => $projectsArray,
                'errors' => $errors,
            ]);
        }

        return $this->render('dashboard/project-groups/edit.html.twig', [
            'isSuccess' => '',
            'projectGroup' => $projectGroup,
            'projectsArray' => $projectsArray,
            'form' => $form,
        ]);

    }

    #[Route('/project-groups/', name: 'app_user_panel_project_group')]
    public function projectGroup(Request $request): Response
    {

        $projectGroupRepository = $this->projectGroupRepository;
        $projectGroups = $projectGroupRepository->findAll();

        if ($projectGroups) {
            $numberTotalGroups = sizeof($projectGroups);

            foreach ($projectGroups as $projectGroup) {
                $numberTotalGroups += sizeof($projectGroup->getProjects());
            }
        }

        return $this->render('dashboard/project-groups/all.html.twig', [
            'projectGroups' => $projectGroups,
            'numberTotalGroups' => $numberTotalGroups,
        ]);

    }

    #[Route('/project-groups/{id}/', name: 'app_user_panel_project_group_single')]
    public function singleProjectGroup(ProjectGroup $projectGroup): Response
    {
        return $this->render('dashboard/project-groups/single.html.twig', [
            'projectGroup' => $projectGroup,
        ]);

    }

    #[Route('/project-groups/{id}/delete/', name: 'app_user_panel_project_group_delete')]
    public function deleteProjectGroup(ProjectGroup $projectGroup): Response
    {
        return $this->render('dashboard/project-groups/delete.html.twig', [
            'projectGroup' => $projectGroup,
        ]);
    }

    #[Route('/project-groups/{id}/delete/confirm/', name: 'app_user_panel_project_group_delete_confirm')]
    public function deleteProjectGroupConfirmed(ProjectGroup $projectGroup): Response
    {
        $this->projectGroupRepository->remove($projectGroup, $this->projectRepository);
        return $this->redirectToRoute('app_user_panel_project_group');
    }

}
