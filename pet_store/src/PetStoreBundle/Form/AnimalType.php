<?php

namespace PetStoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AnimalType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class)
                ->add('breed', TextType::class)
                ->add('gender', TextType::class)
                ->add('color', TextType::class)
                ->add('price', TextType::class)
                ->add('image', TextType::class)
                ->add('inStock', TextType::class)
                ->add('description', TextType::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PetStoreBundle\Entity\Animal'
        ));
    }

}
