<?php

namespace App\Controller;

use App\Entity\Link;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RedirectLinkController extends AbstractController
{

    protected $entityManager;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->entityManager = $doctrine->getManager();
    }

    public function redirectLink($hash)
    {
        $link = $this->entityManager->getRepository(Link::class)->findOneBy([
            'hash' => $hash,
        ]);

        if ($link) {

            $link->incrementCount();
            $this->entityManager->flush();

            return $this->redirect($link->getLink());
        }

        if (!$link) {
            return $this->render('404.html.twig');
        }
    }


}
