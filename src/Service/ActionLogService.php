<?php

namespace App\Service;

use App\Entity\ActionLog;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class ActionLogService
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security
    ) {
    }

    public function enregistrer(string $action, string $entite, int $entiteId): void
    {
        $log = new ActionLog();
        $log->setAction($action);
        $log->setEntite($entite);
        $log->setEntiteId($entiteId);
        $log->setDateAction(new \DateTime());

        $user = $this->security->getUser();
        if ($user instanceof User) {
            $log->setUser($user);
        }

        $this->em->persist($log);
        $this->em->flush();
    }
}
