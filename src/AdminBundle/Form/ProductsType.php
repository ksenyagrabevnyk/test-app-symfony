<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
class ProductsType extends AbstractType
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
                'label' => 'Name',
            ])
            ->add('categoryId', null, [
                'required' => true,
                'class' => 'AdminBundle\Entity\Categories',
                'label' => 'Category',
            ])
            ->add('salePrice', null, [
                'required' => false,
                'label' => 'Sale price',
            ])
            ->add('purchasePrice', null, [
                'required' => false,
                'label' => 'Purchase price',
            ])
            ->add('profit', null, [
                'required' => false,
                'label' => 'Profit',
            ])
//            ->add('imgPath', 'file', [
//                'data_class' => null,
//                'required' => false,
//                'label' => 'Image',
//            ])
            ->add('imgPath', FileType::class, [
                'data_class' => null,
                'label' => 'Brochure (PDF file)'
            ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AdminBundle\Entity\Products'
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'adminbundle_products';
    }
}
