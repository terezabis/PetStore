<?php

namespace PetStoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use PetStoreBundle\Entity\Category;

class AnimalType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('name', TextType::class)
                ->add('breed', TextType::class)
                ->add('gender', ChoiceType::class, array(
                    'choices' => array(
                        'Male' => 'Male',
                        'Female' => 'Female'
                    ),
                    'expanded' => true,
                    'multiple' => false,
                    'choices_as_values' => true,
                    'label' => false
                ))
                ->add('age', NumberType::class)
                ->add('color', TextType::class)
                ->add('price', NumberType::class)
                ->add('image', TextType::class)
                ->add('inStock', ChoiceType::class, array(
                    'choices' => array(
                        'In stock' => true,
                        'Out of stock' => false
                    ),
                    'expanded' => true,
                    'multiple' => false,
                    'choices_as_values' => true,
                    'label' => false
                ))
                ->add('description', TextType::class)
                ->add('category', EntityType::class, array(
                    'class' => Category::class,
                    'choice_label' => 'name',
                    'placeholder' => 'Select a Category...',
                    'label' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'PetStoreBundle\Entity\Animal'
        ));
    }

}
