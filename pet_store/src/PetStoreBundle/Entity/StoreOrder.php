<?php

namespace PetStoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StoreOrder
 *
 * @ORM\Table(name="store_orders")
 * @ORM\Entity(repositoryClass="PetStoreBundle\Repository\StoreOrderRepository")
 */
class StoreOrder
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="order_date", type="datetime")
     */
    private $orderDate;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;
    
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="PetStoreBundle\Entity\User", inversedBy="store_orders")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     */
    private $user;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_finished", type="boolean", options={"default":"0"})
     */
    private $isFinished;
    
       
    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="PetStoreBundle\Entity\Animal")
     *
     * @ORM\JoinTable(name="store_orders_animals",
     *     joinColumns={@ORM\JoinColumn(name="store_order_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="animal_id", referencedColumnName="id")}
     *   )
     */
    private $animals;
    
    public function __construct() {
        //$this->$orderDate = new \DateTime('NOW');
        $this->animals = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set orderDate
     *
     * @param \DateTime $orderDate
     *
     * @return StoreOrder
     */
    public function setOrderDate($orderDate)
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    /**
     * Get orderDate
     *
     * @return \DateTime
     */
    public function getOrderDate()
    {       
        return $this->orderDate ->format('d-m-Y H:i');
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return StoreOrder
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }
    
    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return StoreOrder
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
        return $this;
    }
    
    /**
     * Set isFinished
     *
     * @param boolean $isFinished
     *
     * @return StoreOrder
     */
    public function setIsFinished($isFinished)
    {
        $this->isFinished = $isFinished;

        return $this;
    }

    /**
     * Get isFinished
     *
     * @return bool
     */
    public function getIsFinished()
    {
        return $this->isFinished;
    }
    
    /**     
     * @return (Animal|Animal)[]
     */
    public function getAnimals()
    {
        $animals = [];

        foreach ($this->animals as $animal){
            /** @var Animal $animal */
            $animals[] = $animal;
        }

        return $animals;
    }
    
    /**
     * @param Animal|null $animal
     * @return StoreOrder
     */
    public function addAnimal(Animal $animal = null)
    {
        $this->animals[] = $animal;
        return $this;
    }
    
    
}

