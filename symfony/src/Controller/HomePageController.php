<?php

namespace App\Controller;

use App\Entity\Link;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomePageController extends AbstractController
{

    public function index(Request $request, ManagerRegistry $doctrine): Response
    {

        $entityManager = $doctrine->getManager();

        $allUserLinks = $entityManager->getRepository(Link::class)->findBy([
            'ip' => $this->getUserIp(),
        ]);

        $link = new Link();

        $form = $this->createFormBuilder($link)
            ->add('link', TextType::class, ['label' => 'Ссылка'])
            ->add('save', SubmitType::class, ['label' => 'Сгенерировать короткую ссылку'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $link = $entityManager->getRepository(Link::class)->findBy([
                'link' => $form->get('link')->getData(),
            ]);

            if($link) {
                return $this->render('home_page/index.html.twig', [
                    'form' => $form->createView(),
                    'save' => true,
                    'haveLink' => true,
                    'allUserLinks' => array_reverse($allUserLinks),
                ]);
            }

            $link = $form->getData();
            $hash = md5($link->getLink() . $link->getIp());
            $link->setHash($hash);
            $link->setIp($this->getUserIp());

            $entityManager->persist($link);
            $entityManager->flush();

        }

        $allUserLinks = $entityManager->getRepository(Link::class)->findBy([
            'ip' => $this->getUserIp(),
        ]);

        $userLinks = $doctrine
        ->getRepository(Link::class)
        ->findBy(['ip' => $this->getUserIp()]);

        return $this->render('home_page/index.html.twig', [
            'form' => $form->createView(),
            'save' => false,
            'allUserLinks' => array_reverse($allUserLinks),
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

    public function saveLink(Request $request, $link)
    {
//
    }

}
