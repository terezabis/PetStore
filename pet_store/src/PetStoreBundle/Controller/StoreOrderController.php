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
                $this->createOrder($currentUser, $animal);
            }
        } else {
            $this->createOrder($currentUser, $animal);
        }
        return $this->redirectToRoute("order_view");
    }
    
    /**
     * @Route("/order/view", name="order_view")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewUnfinishedOrderAction()
    {
        $currentUser = $this->getUser();        
        $order = $this->getUnfinishedOrder($currentUser);
                
        return $this->render("order/view.html.twig",
            ['order' => $order]);
    }
    
    /**
     * @Route("/order/animal_remove/{id}", name="order_animal_remove")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAnimalFromOrderAction(Request $request, $id) 
    {
        $currentUser = $this->getUser();        
        $order = $this->getUnfinishedOrder($currentUser);

        $this->deleteAnimalFromOrder($order->getId(), $id);
        
        return $this->redirectToRoute("order_view");
    }
    
    /**
     * @Route("/order/finish/{id}", name="order_finish")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function finishOrderAction($id)
    {
        $order = $this->getDoctrine()
                      ->getRepository(StoreOrder::class)
                      ->find($id);

        if ($order === null) {
            return $this->redirectToRoute("homepage");
        }
        
        $order->setOrderDate(new DateTime('NOW'));
        $order->setIsFinished(1);
        
        $animalsInOrder = $order->getAnimals();
        
        foreach ($animalsInOrder as $animal){
            $animal->setInStock(false);
        }
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();
                
        return $this->redirectToRoute("order_all");
    }
    
    /**
     * @Route("order/all", name="order_all")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function allUserOrdersAction(Request $request)
    {        
        $currentUser = $this->getUser();
        $orders = $this->getDoctrine()
                ->getRepository(StoreOrder::class)
                ->findBy(['userId' => $currentUser->getId(),'isFinished' => 1,], ['orderDate' => 'DESC']);

        return $this->render("order/all.html.twig", [
            'orders' => $orders ]);
    }
    
    /**
     * @Route("order/admin_all", name="order_admin_all")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function allOrdersAction(Request $request)
    {        
        $currentUser = $this->getUser();
        
        if(!$currentUser->isAdmin())
            return $this->redirectToRoute("homepage");
        
        $orders = $this->getDoctrine()
                ->getRepository(StoreOrder::class)
                ->findBy(['isFinished' => 1,], ['orderDate' => 'DESC']);

        return $this->render("order/admin_all.html.twig", [
            'orders' => $orders ]);
    }
    
    /**
     * @Route("/order/delete/{id}", name="order_delete")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteOrderAction(Request $request, $id) 
    {
        $order = $this
                ->getDoctrine()
                ->getRepository(StoreOrder::class)
                ->find($id);

        if ($order === null) {
            return $this->redirectToRoute("homepage");
        }
        
        $currentUser = $this->getUser();
        
        if(!$currentUser->isAdmin())
            return $this->redirectToRoute("homepage");

        $em = $this->getDoctrine()->getManager();
        $em->remove($order);
        $em->flush();

        return $this->redirectToRoute("order_admin_all");
    }

    private function createOrder(User $user, Animal $animal) 
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
    
    protected function getUnfinishedOrder(User $user)
    {
        $order = null;
        
        if ($this->getDoctrine()->getRepository(StoreOrder::class)->findOneBy(['userId' => $user->getId(),'isFinished' => 0,])) {
            $order = $this->getDoctrine()->getRepository(StoreOrder::class)->findOneBy([
                'userId' => $user->getId(),
                'isFinished' => 0,]);
            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush();
        }
        
        return $order;
    }
    
    private function deleteAnimalFromOrder($storeOrderId, $animalId)
    {
        $sql = "DELETE FROM store_orders_animals WHERE store_order_id = :storeOrderId and animal_id = :animalId";
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();   
        $statement = $connection->prepare($sql);
        $statement->bindValue('storeOrderId', $storeOrderId);
        $statement->bindValue('animalId', $animalId);
        $statement->execute();
    }

}
