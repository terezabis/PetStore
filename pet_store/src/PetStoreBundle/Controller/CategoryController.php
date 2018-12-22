<?php

namespace PetStoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use PetStoreBundle\Entity\Category;
use PetStoreBundle\Form\CategoryType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends Controller 
{

    /**
     * @Route("/category/create", name="category_create")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request) 
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute("category_all");
        }

        return $this->render('category/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/category/edit/{id}", name="category_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id) 
    {
        $category = $this
                ->getDoctrine()
                ->getRepository(Category::class)
                ->find($id);

        if ($category === null) {
            return $this->redirectToRoute("homepage");
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->merge($category);
            $em->flush();

            return $this->redirectToRoute("category_all");
        }

        return $this->render('category/edit.html.twig', ['form' => $form->createView(),
                    'category' => $category]);
    }

    /**
     * @Route("/category/all", name="category_all")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function allCategories() 
    {
        $categories = $this->getDoctrine()
                ->getRepository(Category::class)
                ->findAll();

        return $this->render("category/all.html.twig", ['categories' => $categories]);
    }

}
