<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function save(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllNew()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.status = :status')
            ->setParameter('status', 'new')
            ->orderBy('t.lastChangedDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllFailed()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.status = :status')
            ->setParameter('status', 'failed')
            ->orderBy('t.lastChangedDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllPending()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.status = :status')
            ->setParameter('status', 'pending')
            ->orderBy('t.lastChangedDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function setStatusPending(Task $task): void
    {
        $task->setStatus('pending');
        $task->setLastChangedDate(new \DateTime('now', new \DateTimeZone('Europe/Warsaw')));
        $this->getEntityManager()->persist($task);
        $this->getEntityManager()->flush();
    }

    public function setStatusFailed(Task $task): void
    {
        $task->setStatus('failed');
        $task->setLastChangedDate(new \DateTime('now', new \DateTimeZone('Europe/Warsaw')));
        $this->getEntityManager()->persist($task);
        $this->getEntityManager()->flush();
    }

}
