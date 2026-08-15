<?php

namespace App\Controller\Admin;

use App\Repository\ActionLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/journal')]
class JournalController extends AbstractController
{
    #[Route('', name: 'app_admin_journal_index', methods: ['GET'])]
    public function index(ActionLogRepository $actionLogRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $logs = $actionLogRepository->findBy([], ['dateAction' => 'DESC'], 100);

        return $this->render('admin/journal/index.html.twig', [
            'logs' => $logs,
        ]);
    }
}
