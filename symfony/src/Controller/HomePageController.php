<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{

    public function index(): Response
    {

        return $this->render('home_page/index.html.twig', [
            'ip' => $this->getUserIp(),
        ]);
    }

    /**
     * Return User Ip
     * Need for identity user and user links
     *
     * @return string
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getUserIp(): string
    {
        return $this->container->get('request_stack')->getCurrentRequest()->getClientIp();
    }

}
