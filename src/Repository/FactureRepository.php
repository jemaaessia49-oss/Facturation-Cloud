<?php

namespace App\Repository;

use App\Entity\Facture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Facture>
 */
class FactureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Facture::class);
    }

    public function getMontantTotal(): float
    {
        return (float) ($this->createQueryBuilder('f')
            ->select('COALESCE(SUM(f.montantTotal), 0)')
            ->getQuery()
            ->getSingleScalarResult() ?? 0);
    }

    public function countByStatut(): array
    {
        $rows = $this->createQueryBuilder('f')
            ->select('f.statut AS statut, COUNT(f.id) AS total')
            ->groupBy('f.statut')
            ->getQuery()
            ->getArrayResult();

        $result = [];
        foreach ($rows as $row) {
            $result[$row['statut']] = (int) $row['total'];
        }

        return $result;
    }

    /**
     * Montants facturés des 12 derniers mois (année courante prioritaire).
     *
     * @return array{labels: list<string>, values: list<float>}
     */
    public function getMontantsMensuels(int $annee): array
    {
        $rows = $this->createQueryBuilder('f')
            ->select('f.mois AS mois, COALESCE(SUM(f.montantTotal), 0) AS total')
            ->andWhere('f.annee = :annee')
            ->setParameter('annee', $annee)
            ->groupBy('f.mois')
            ->orderBy('f.mois', 'ASC')
            ->getQuery()
            ->getArrayResult();

        $byMonth = [];
        foreach ($rows as $row) {
            $byMonth[(int) $row['mois']] = (float) $row['total'];
        }

        $moisLabels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
        $labels = [];
        $values = [];

        for ($m = 1; $m <= 12; ++$m) {
            $labels[] = $moisLabels[$m - 1];
            $values[] = $byMonth[$m] ?? 0.0;
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * @return list<Facture>
     */
    public function findRecent(int $limit = 5): array
    {
        return $this->createQueryBuilder('f')
            ->leftJoin('f.projet', 'p')
            ->addSelect('p')
            ->orderBy('f.annee', 'DESC')
            ->addOrderBy('f.mois', 'DESC')
            ->addOrderBy('f.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
