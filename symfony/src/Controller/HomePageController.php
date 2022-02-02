<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    public function index(): Response
    {

        return phpinfo();
//        return $this->render('home_page/index.html.twig', [
//            'controller_name' => 'HomePageController',
//        ]);
    }
}
