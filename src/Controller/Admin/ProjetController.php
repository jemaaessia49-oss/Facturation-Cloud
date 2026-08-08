<?php

namespace App\Controller\Admin;

use App\Entity\Projet;
use App\Form\ProjetType;
use App\Repository\ProjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/projets')]
class ProjetController extends AbstractController
{
    #[Route('', name: 'app_admin_projet_index', methods: ['GET'])]
    public function index(ProjetRepository $projetRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/projet/index.html.twig', [
            'projets' => $projetRepository->findAll(),
        ]);
    }

    #[Route('/nouveau', name: 'app_admin_projet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $projet = new Projet();
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($projet);
            $em->flush();

            $this->addFlash('success', 'Projet cree avec succes.');

            return $this->redirectToRoute('app_admin_projet_index');
        }

        return $this->render('admin/projet/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_admin_projet_edit', methods: ['GET', 'POST'])]
    public function edit(Projet $projet, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Projet modifie avec succes.');

            return $this->redirectToRoute('app_admin_projet_index');
        }

        return $this->render('admin/projet/edit.html.twig', [
            'form' => $form,
            'projet' => $projet,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'app_admin_projet_delete', methods: ['POST'])]
    public function delete(Projet $projet, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete_projet_'.$projet->getId(), $request->getPayload()->getString('_token'))) {
            $em->remove($projet);
            $em->flush();
            $this->addFlash('success', 'Projet supprime.');
        }

        return $this->redirectToRoute('app_admin_projet_index');
    }
}