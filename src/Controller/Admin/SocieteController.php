<?php

namespace App\Controller\Admin;

use App\Entity\Societe;
use App\Form\SocieteType;
use App\Repository\SocieteRepository;
use App\Service\ActionLogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/societes')]
class SocieteController extends AbstractController
{
    #[Route('', name: 'app_admin_societe_index', methods: ['GET'])]
    public function index(SocieteRepository $societeRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/societe/index.html.twig', [
            'societes' => $societeRepository->findAll(),
        ]);
    }

    #[Route('/nouveau', name: 'app_admin_societe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, ActionLogService $actionLogService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $societe = new Societe();
        $form = $this->createForm(SocieteType::class, $societe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($societe);
            $em->flush();

            $actionLogService->enregistrer('Creation societe', 'Societe', $societe->getId());

            $this->addFlash('success', 'Société créée avec succès.');

            return $this->redirectToRoute('app_admin_societe_index');
        }

        return $this->render('admin/societe/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_admin_societe_edit', methods: ['GET', 'POST'])]
    public function edit(Societe $societe, Request $request, EntityManagerInterface $em, ActionLogService $actionLogService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(SocieteType::class, $societe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $actionLogService->enregistrer('Modification societe', 'Societe', $societe->getId());

            $this->addFlash('success', 'Société modifiée avec succès.');

            return $this->redirectToRoute('app_admin_societe_index');
        }

        return $this->render('admin/societe/edit.html.twig', [
            'form' => $form,
            'societe' => $societe,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'app_admin_societe_delete', methods: ['POST'])]
    public function delete(Societe $societe, Request $request, EntityManagerInterface $em, ActionLogService $actionLogService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete_societe_'.$societe->getId(), $request->getPayload()->getString('_token'))) {
            $societeId = $societe->getId();

            $em->remove($societe);
            $em->flush();

            $actionLogService->enregistrer('Suppression societe', 'Societe', $societeId);

            $this->addFlash('success', 'Société supprimée.');
        }

        return $this->redirectToRoute('app_admin_societe_index');
    }
}
