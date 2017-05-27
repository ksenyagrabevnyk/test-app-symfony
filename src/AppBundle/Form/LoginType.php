<?php


namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('_username', 'text', array(
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Username'
                )
            ))
            ->add('_password', 'password', array(
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Password'
                )
            ))
        ;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return '';
    }
}