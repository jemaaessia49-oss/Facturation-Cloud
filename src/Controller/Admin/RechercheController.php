<?php

namespace App\Controller\Admin;

use App\Repository\FactureRepository;
use App\Repository\SocieteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/recherche')]
class RechercheController extends AbstractController
{
    #[Route('/factures', name: 'app_admin_recherche_factures', methods: ['GET'])]
    public function factures(Request $request, EntityManagerInterface $em, SocieteRepository $societeRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $societeId = $request->query->get('societe');
        $numeroSo = $request->query->get('numero_so');
        $mois = $request->query->get('mois');
        $annee = $request->query->get('annee');
        $statut = $request->query->get('statut');

        $qb = $em->createQueryBuilder()
            ->select('f', 'p', 's')
            ->from('App\Entity\Facture', 'f')
            ->join('f.projet', 'p')
            ->join('p.societe', 's')
            ->orderBy('f.annee', 'DESC')
            ->addOrderBy('f.mois', 'DESC');

        if ($societeId) {
            $qb->andWhere('s.id = :societeId')->setParameter('societeId', $societeId);
        }

        if ($numeroSo) {
            $qb->andWhere('p.numeroSo LIKE :numeroSo')->setParameter('numeroSo', '%'.$numeroSo.'%');
        }

        if ($mois) {
            $qb->andWhere('f.mois = :mois')->setParameter('mois', $mois);
        }

        if ($annee) {
            $qb->andWhere('f.annee = :annee')->setParameter('annee', $annee);
        }

        if ($statut) {
            $qb->andWhere('f.statut = :statut')->setParameter('statut', $statut);
        }

        $factures = $qb->getQuery()->getResult();

        return $this->render('admin/recherche/factures.html.twig', [
            'factures' => $factures,
            'societes' => $societeRepository->findAll(),
            'criteres' => [
                'societe' => $societeId,
                'numero_so' => $numeroSo,
                'mois' => $mois,
                'annee' => $annee,
                'statut' => $statut,
            ],
        ]);
    }
}
