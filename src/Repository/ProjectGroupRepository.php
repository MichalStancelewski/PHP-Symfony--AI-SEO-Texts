<?php

namespace App\Repository;

use App\Entity\DomainGroup;
use App\Entity\ProjectGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjectGroup>
 *
 * @method ProjectGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectGroup[]    findAll()
 * @method ProjectGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectGroup::class);
    }

    public function save(ProjectGroup $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProjectGroup $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function edit(ProjectGroup $projectGroup, string $name, ?DomainGroup $domainGroup, array $oldProjects, array $newProjects): void
    {
        $projectGroup->setName($name);

        if ($domainGroup) {
            $projectGroup->setDomainGroup($domainGroup);
        } else {
            $projectGroup->setDomainGroup(null);
        }

        if ($oldProjects) {
            foreach ($oldProjects as $p) {
                $projectGroup->removeProject($p);
            }
        }

        if ($newProjects) {
            foreach ($newProjects as $p) {
                $projectGroup->addProject($p);
            }
        }

        $entityManager = $this->getEntityManager();
        $entityManager->persist($projectGroup);
        $entityManager->flush();
    }
}
