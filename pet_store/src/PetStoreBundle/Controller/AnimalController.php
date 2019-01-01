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

class AnimalController extends Controller {

    /**
     * @Route("/animal/create", name="animal_create")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request) 
    {
        $animal = new Animal();
        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);
        
        $currentUser = $this->getUser();
        
        if(!$currentUser->isAdmin())
            return $this->redirectToRoute("homepage");

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($animal);
            $em->flush();

            return $this->redirectToRoute("homepage");
        }

        return $this->render('animal/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/animal/edit/{id}", name="animal_edit")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        $animal = $this
                ->getDoctrine()
                ->getRepository(Animal::class)
                ->find($id);

        if ($animal === null) {
            return $this->redirectToRoute("homepage");
        }
        
        $currentUser = $this->getUser();
        
        if(!$currentUser->isAdmin())
            return $this->redirectToRoute("homepage");

        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->merge($animal);
            $em->flush();

            return $this->redirectToRoute("homepage");
        }

        return $this->render('animal/edit.html.twig', ['form' => $form->createView(),
                    'animal' => $animal]);
    }
    
    /**
     * @Route("/animal/{id}", name="animal_details")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAnimal($id)
    {
        /** @var Animal $animal */
        $animal = $this
            ->getDoctrine()
            ->getRepository(Animal::class)
            ->find($id);
       
        $em = $this->getDoctrine()->getManager();
        $em->persist($animal);
        $em->flush();

        return $this->render("animal/details.html.twig",
            ['animal' => $animal]);
    }

    /**
     * @Route("/animal/delete/{id}", name="animal_delete")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, $id) 
    {
        $animal = $this
                ->getDoctrine()
                ->getRepository(Animal::class)
                ->find($id);

        if ($animal === null) {
            return $this->redirectToRoute("homepage");
        }
        
        $currentUser = $this->getUser();
        
        if(!$currentUser->isAdmin())
            return $this->redirectToRoute("homepage");

        $em = $this->getDoctrine()->getManager();
        $em->remove($animal);
        $em->flush();

        return $this->redirectToRoute("homepage");
    }
    
    /**
     * @Route("animal/category/{id}", name="animal_category")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function allByCategoryAction(Request $request, $id)
    {               
        $animals = $this->getDoctrine()
                ->getRepository(Animal::class)
                ->findBy(['categoryId' => $id], ['inStock' => 'DESC', 'id' => 'DESC']);
        
        $category = $this->getDoctrine()
                ->getRepository(Category::class)
                ->find($id);

        return $this->render("animal/by_category.html.twig", [
            'animals' => $animals, 'category' => $category]);
    }

}
