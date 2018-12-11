<?php

namespace PetStoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use PetStoreBundle\Entity\Animal;
use PetStoreBundle\Entity\Category;
use PetStoreBundle\Form\AnimalType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;

class AnimalController extends Controller 
{
    /**
     * @Route("/animal/create", name="animal_create")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $animal = new Animal();
        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $animal->setCategoryId(1);
            
            //$category->addAnimal($animal);

            $em = $this->getDoctrine()->getManager();
            $category = $em->find('Category', 1);
            $animal->setCategory($category);
            $category->addAnimal($animal);
            
            $em->persist($animal);
            $em->flush();

            return $this->redirectToRoute("homepage");
        }

        return $this->render('animal/create.html.twig',
            ['form' => $form->createView()]);
    }
}
