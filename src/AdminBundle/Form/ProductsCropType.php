<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ProductsCropType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'required' => true,
                'label' => 'Название товара',
            ])
            ->add('categoryId', null, [
                'required' => true,
                'class' => 'AdminBundle\Entity\Categories',
                'label' => 'Выбрать категорию',
            ])
            ->add('salePrice', null, [
                'required' => false,
                'label' => 'Скидка',
            ])
            ->add('purchasePrice', null, [
                'required' => false,
                'label' => 'Цена товара',
            ])
            ->add('profit', null, [
                'required' => false,
                'label' => 'Маржа',
            ])
            ->add('imgPath', HiddenType::class, [
                'attr' => [
                    'class' => 'photo-file'
                    ],
                ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'adminbundle_products_crop';
    }
}
