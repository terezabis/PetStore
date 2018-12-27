<?php

namespace PetStoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use PetStoreBundle\Entity\Animal;

class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {               
        $animals = $this->getDoctrine()
                ->getRepository(Animal::class)
                ->findBy([], ['inStock' => 'DESC', 'id' => 'DESC']);

        return $this->render("home/index.html.twig", [
            'animals' => $animals,
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            ]);
    }
}
