<?php

namespace App\Repository;

use App\Entity\Projet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Projet>
 */
class ProjetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Projet::class);
    }

    public function countByStatut(): array
    {
        $rows = $this->createQueryBuilder('p')
            ->select('p.statut AS statut, COUNT(p.id) AS total')
            ->groupBy('p.statut')
            ->getQuery()
            ->getArrayResult();

        $result = [];
        foreach ($rows as $row) {
            $result[$row['statut']] = (int) $row['total'];
        }

        return $result;
    }

    /**
     * @return list<Projet>
     */
    public function findRecent(int $limit = 5): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.societe', 's')
            ->addSelect('s')
            ->orderBy('p.dateDebut', 'DESC')
            ->addOrderBy('p.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
