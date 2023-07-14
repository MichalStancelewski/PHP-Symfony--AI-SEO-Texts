<?php

namespace App\Repository;

use App\Entity\DomainGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DomainGroup>
 *
 * @method DomainGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method DomainGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method DomainGroup[]    findAll()
 * @method DomainGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DomainGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DomainGroup::class);
    }

    public function save(DomainGroup $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DomainGroup $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function edit(DomainGroup $domainGroup, string $name, array $oldDomains, array $newDomains): void
    {
        $domainGroup->setName($name);

        if ($oldDomains) {
            foreach ($oldDomains as $d) {
                $domainGroup->removeDomain($d);
            }
        }

        if ($newDomains) {
            foreach ($newDomains as $d) {
                $domainGroup->addDomain($d);
            }
        }

        $entityManager = $this->getEntityManager();
        $entityManager->persist($domainGroup);
        $entityManager->flush();
    }

}
