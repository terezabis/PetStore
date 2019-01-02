<?php

namespace PetStoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use PetStoreBundle\Entity\Animal;
use Doctrine\ORM\Tools\Pagination\Paginator;

class HomeController extends Controller
{
    const LIMIT = 8;
    
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
        
        $paginator  = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $animals, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            self::LIMIT/*limit per page*/
        );

        return $this->render("home/index.html.twig", [
            'pagination' => $pagination,
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            ]);
    }
}
