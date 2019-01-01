<?php

namespace PetStoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType 
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('email', EmailType::class, array(
                    'label' => false
                ))
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => array('label' => 'Password', 'attr' => array(
                            'class' => 'form-control'
                        )),
                    'second_options' => array('label' => 'Repeat password', 'attr' => array(
                            'class' => 'form-control'
                        )),
                ])
                ->add('firstName', TextType::class, array(
                    'label' => false
                ))
                ->add('lastName', TextType::class, array(
                    'label' => false
                ))
                ->add('phone', TextType::class, array(
                    'label' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'PetStoreBundle\Entity\User'
        ));
    }

}
