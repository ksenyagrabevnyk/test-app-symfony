<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
                'label' => 'username',
                'attr' => array(
                    'placeholder' => 'Ваш логин'
                )
            ))
            ->add('_password', 'password', array(
                'required' => true,
                'label' => 'sssss',
                'attr' => array(
                    'placeholder' => 'Пароль'
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

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolverInterface $resolver The resolver for the options
     *
     * @deprecated since version 2.7, to be renamed in 3.0.
     *             Use the method configureOptions instead. This method will be
     *             added to the FormTypeInterface with Symfony 3.0.
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        // TODO: Implement setDefaultOptions() method.
    }
}