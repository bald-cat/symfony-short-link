<?php

namespace App\Controller;

use App\Entity\Link;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;


class HomePageController extends AbstractController
{

    protected $entityManager;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->entityManager = $doctrine->getManager();
    }

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {

        $form = $this->getForm();
        $newLink = false;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $link = $this->entityManager->getRepository(Link::class)->findBy([
                'link' => $form->get('link')->getData(),
            ]);

            if($link) {
                return $this->render('home_page/index.html.twig', [
                    'form' => $form->createView(),
                    'haveLink' => true,
                    'allUserLinks' => $this->getAllUserLinks(),
                    'domain' => $this->getParameter('domain'),
                ]);
            }

            $link = $form->getData();
            $hash = md5($link->getLink() . $link->getIp());
            $link->setHash($hash);
            $link->setIp($this->getUserIp());
            $link->startCount();
            $newLink = $link->getLink();
            $this->entityManager->persist($link);
            $this->entityManager->flush();

        }

        return $this->render('home_page/index.html.twig', [
            'form' => $form->createView(),
            'allUserLinks' => $this->getAllUserLinks(),
            'domain' => $this->getParameter('domain'),
            'save' => true,
            'newLink' => $newLink,
        ]);

    }

    public function getForm()
    {

        $link = new Link();

        $form = $this->createFormBuilder($link)
            ->add('link', TextType::class, ['label' => '????????????'])
            ->add('save', SubmitType::class, ['label' => '?????????????????????????? ???????????????? ????????????'])
            ->getForm();

        return $form;
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

    /**
     * Method for return all user links
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getAllUserLinks(): array
    {
        return array_reverse($this->entityManager->getRepository(Link::class)->findBy([
            'ip' => $this->getUserIp(),
        ]));
    }

}
