<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/utilisateurs')]
class UserController extends AbstractController
{
    #[Route('', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/nouveau', name: 'app_admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['is_edit' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles([$form->get('role')->getData()]);
            $user->setPassword(
                $passwordHasher->hashPassword($user, $form->get('plainPassword')->getData())
            );

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Utilisateur créé avec succès.');

            return $this->redirectToRoute('app_admin_user_index');
        }

        return $this->render('admin/user/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(User $user, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $currentRole = $user->getRoles()[0] ?? 'ROLE_CONSULTANT';

        $form = $this->createForm(UserType::class, $user, [
            'is_edit' => true,
            'current_role' => $currentRole,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles([$form->get('role')->getData()]);

            $plainPassword = $form->get('plainPassword')->getData();
            if (!empty($plainPassword)) {
                $user->setPassword(
                    $passwordHasher->hashPassword($user, $plainPassword)
                );
            }

            $em->flush();

            $this->addFlash('success', 'Utilisateur modifié avec succès.');

            return $this->redirectToRoute('app_admin_user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete_user_'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', 'Utilisateur supprimé.');
        }

        return $this->redirectToRoute('app_admin_user_index');
    }
}