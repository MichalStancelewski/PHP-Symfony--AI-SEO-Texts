<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function save(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function setStatusDone(Project $project): void
    {
        $project->setStatus('done');
        $this->getEntityManager()->persist($project);
        $this->getEntityManager()->flush();
    }

    public function setStatusPending(Project $project): void
    {
        $project->setStatus('pending');
        $this->getEntityManager()->persist($project);
        $this->getEntityManager()->flush();
    }

    public function findAllPending()
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', 'pending')
            ->orderBy('p.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllDone()
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', 'done')
            ->orderBy('p.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findNewest(int $number)
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.date', 'DESC')
            ->setMaxResults($number)
            ->getQuery()
            ->getResult();
    }


}
