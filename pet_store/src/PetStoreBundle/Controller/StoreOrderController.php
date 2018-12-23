<?php

namespace PetStoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use PetStoreBundle\Entity\Animal;
use PetStoreBundle\Entity\User;
use PetStoreBundle\Entity\StoreOrder;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use \Datetime;

class StoreOrderController extends Controller
{

    /**
     * @Route("/add/{id}", name="animal_to_order")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAnimalToOrderAction($id) 
    {
        $animal = $this
                ->getDoctrine()
                ->getRepository(Animal::class)
                ->find($id);

        if ($animal === null) {
            return $this->redirectToRoute("homepage");
        }

        $currentUser = $this->getUser();

        $order = null;

        if (count($currentUser->getStoreOrders()) > 0) {
            if ($this->getDoctrine()->getRepository(StoreOrder::class)->findOneBy([
                        'userId' => $currentUser->getId(),
                        'isFinished' => 0,
                    ])) {
                $order = $this->getDoctrine()->getRepository(StoreOrder::class)->findOneBy([
                    'userId' => $currentUser->getId(),
                    'isFinished' => 0,
                ]);

                if (!in_array($animal, $order->getAnimals())) {
                    $order->addAnimal($animal);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($order);
                    $em->flush();

                    $animal->addStoreOrder($order);
                } else {
                    return $this->redirectToRoute("homepage");
                }
            } else {
                CreateOrder($currentUser, $animal);
            }
        } else {
            CreateOrder($currentUser, $animal);
        }
        return $this->redirectToRoute("homepage");
    }
    
    /**
     * @Route("/order/view", name="order_view")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewUnfinishedOrder()
    {
        $currentUser = $this->getUser();
        $order = null;
        
        if ($this->getDoctrine()->getRepository(StoreOrder::class)->findOneBy(['userId' => $currentUser->getId(),'isFinished' => 0,])) {
            $order = $this->getDoctrine()->getRepository(StoreOrder::class)->findOneBy([
                'userId' => $currentUser->getId(),
                'isFinished' => 0,]);
            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush();
        }
        
        $animalsInOrder = $order->getAnimals();  
        
        return $this->render("order/view.html.twig",
            ['order' => $order, 'animals' => $animalsInOrder]);
    }

    private function CreateOrder(User $user, Animal $animal) 
    {
        $order = new StoreOrder();
        $order->setUser($user);
        $order->addAnimal($animal);
        $order->setOrderDate(new DateTime('NOW'));
        $order->setIsFinished(0);

        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();

        $animal->addStoreOrder($order);
    }

}
