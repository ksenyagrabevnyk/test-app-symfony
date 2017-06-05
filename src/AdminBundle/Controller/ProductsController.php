<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Products;
use AdminBundle\Entity\Categories;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AdminBundle\Form\ProductsType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use AdminBundle\Services\FileUploaderService;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * Products controller.
 *
 * @Route("/products")
 */
class ProductsController extends Controller
{
    /**
     * Lists all Products entities.
     *
     * @Route("/", name="products")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $user =  $this->get('security.context')->getToken()->getUser();
        $userId = $user->getId();
        $countAllActiveProducts = $em->getRepository(Products::class)
            ->getCountAllActiveProducts($userId);
        $search = $request->get('search');
        $entities = $em->getRepository(Products::class)
            ->getAllProductsQuery($search, $userId);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $request->query->getInt('page', 1),
            10
        );

        if (!count($pagination->getItems()) && !empty($search)) {
            $this->container->get('session')->getFlashBag()->clear();
            $this->get('session')->getFlashBag()->add('error', 'Тема не найдена');
        }

        return $this->render('AdminBundle:Products:index.html.twig', [
            'entities' => $pagination,
            'count'  => $countAllActiveProducts,
        ]);
    }

    /**
     * Creates a new Products entity.
     *
     * @Route("/", name="product_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $entity = new Products();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $categoryId = $request->request->get('adminbundle_products')['categoryId'];
            $category = $em->getRepository(Categories::class)->find($categoryId);
            $entity->setName($request->request->get('adminbundle_products')['name']);
            $entity->setCategoryId($category);
            $entity->setSalePrice($request->request->get('adminbundle_products')['salePrice']);
            $entity->setPurchasePrice($request->request->get('adminbundle_products')['purchasePrice']);
            $entity->setProfit($request->request->get('adminbundle_products')['profit']);
            $entity->setImgPath('uploads/products/'. $this->get('file_uploader')->upload($entity->getImgPath()));
//            if (!empty($entity->getPhotoName())) {
//                $path = 'uploads/products/'.$entity->getId().'/'.$entity->getPhotoName();
//                $entity->setImgPath($path);
//                $em->persist($entity);
//                $em->flush();
//            }

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('products'));
        }

        return $this->render('AdminBundle:Products:new.html.twig', [
            'entity' => $entity,
            'form'   => $form->createView(),
        ]);
    }

//    /**
//     * Creates a new Image for Products entity.
//     *
//     * @Route("/save_img", name="save_img")
//     * @Method("POST")
//     */
//    public function saveAction(Request $request)
//    {
//        $image = $request->request->get('image');
//
//        $preg = '#^data:image/\w+;base64,#i';
//        $imageDecoded = base64_decode(preg_replace($preg, '', $image));
//
//        $info = getimagesize($image);
//
//        $extension = image_type_to_extension($info[2]);
//        $imageName = md5(uniqid(rand(), true))."".$extension;
//
//        $path = $this->get('kernel')->getRootDir().'/../web/tmp/';
//        $filePath = $path.$imageName;
//
//        file_put_contents($filePath, $imageDecoded);
//        die();
//    }

    /**
     * Creates a form to create a Card entity.
     *
     * @param Products $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Products $entity)
    {
        $form = $this->createForm(new ProductsType(), $entity, [
            'action' => $this->generateUrl('product_create'),
            'method' => 'POST',
        ]);

        $form->add('submit', 'submit', [
            'label' => 'Create',
            'attr' => [
                'class' => 'btn btn-danger'
            ]]);

        return $form;
    }

    /**
     * Displays a form to create a new Card entity.
     *
     * @Route("/new", name="product_new")
     * @Method("GET")
     */
    public function newAction()
    {
        $entity = new Products();
        $form   = $this->createCreateForm($entity);

        return $this->render('AdminBundle:Products:new.html.twig', [
            'entity' => $entity,
            'form'   => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Products entity.
     *
     * @Route("/{id}/edit", name="product_edit")
     * @ParamConverter("entity", class="AdminBundle:Products")
     * @Method("GET")
     */
    public function editAction(Products $entity, $id)
    {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find product entity.');
        }
//        $em = $this->get('doctrine.orm.entity_manager');
        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

//        if (!empty($entity->getPhotoName())) {
//            $path = 'uploads/theme/'.$entity->getId().'/'.$entity->getPhotoName();
//            $entity->setImgPath($path);
//            $em->persist($entity);
//            $em->flush();
//        }
//        var_dump(323); die;

        return $this->render('AdminBundle:Products:edit.html.twig', [
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Creates a form to edit a Products entity.
     *
     * @param Products $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Products $entity)
    {
        $form = $this->createForm(new ProductsType(), $entity, [
                'action' => $this->generateUrl(
                'product_update', [
                'id' => $entity->getId()
            ]),
            'method' => 'PUT',
        ]);

        $form->add('submit', 'submit', [
            'label' => 'Update',
            'attr' => ['class' => 'btn btn-danger']
        ])
            ->add('imgPath', FileType::class, [
                'empty_data' => 'uploads/'.$entity->getImgPath(),
                'required' => false,
                'data_class' => null,
            ]);


//        var_dump(22);
//        $form->add('submit', 'submit', ['label' => 'Update']);

//        $form->add('submit', 'submit', [
//            'label' => 'Update',
//            'attr' => ['class' => 'btn btn-danger']
//        ])
//            ->add('photoPath', FileType::class, [
//                'empty_data' => 'uploads/'.$entity->getImgPath(),
//                'required' => false,
//                'data_class' => null,
//            ]);

        return $form;
    }

    /**
     * Edits an existing Products entity.
     *
     * @Route("/{id}", name="product_update",  requirements={"id": "\d+"})
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id)
    {
//        var_dump(44); die;
        $em = $this->get('doctrine.orm.entity_manager');
        $entity = $em->getRepository(Products::class)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find products entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $oldFileName = $entity->getImgPath();
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setImgPath('uploads/products/'. $this->get('file_uploader')->upload($entity->getImgPath()));

//            $entity->setImgPath($this->get('file_uploader')->upload($entity->getImgPath()));
            $em->flush();
            $fs = new Filesystem();
            $fs->remove(['uploads/'.$oldFileName]);
//            if (!empty($entity->getPhotoName())) {
//                $path = 'uploads/product/'.$entity->getId().'/'.$entity->getPhotoName();
//                $entity->setImgPath($path);
//                $em->persist($entity);
//                $em->flush();
//            }

            $categoryId = $request->request->get('adminbundle_products')['categoryId'];
            $category = $em->getRepository(Categories::class)->find($categoryId);
            $entity->setName($request->request->get('adminbundle_products')['name']);
            $entity->setCategoryId($category);
            $entity->setSalePrice($request->request->get('adminbundle_products')['salePrice']);
            $entity->setPurchasePrice($request->request->get('adminbundle_products')['purchasePrice']);
            $entity->setProfit($request->request->get('adminbundle_products')['profit']);
//            $entity->setImgPath($request->request->get('adminbundle_products')['imgPath']);
            $em->flush();

            return $this->redirect($this->generateUrl('products'));
        }

        return $this->render('AdminBundle:Products:edit.html.twig', [
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Products entity.
     *
     * @Route("/{id}", name="product_delete",  requirements={"id": "\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        $em = $this->get('doctrine.orm.entity_manager');
        $entity = $em->getRepository(Products::class)->find($id)->setIsActive(0);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AdminBundle:Products')->find($id);
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Product entity.');
            }

            $fs = new Filesystem();
            $fs->remove(['uploads/'.$entity->getImgPath()]);
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('products'));
    }

//    /**
//     * Deletes a Product picture.
//     *
//     * @Route("/image-ajax-delete", name="image-ajax-delete")
//     * @Method("POST")
//     */
//    public function deletePicAction(Request $request)
//    {
//        $id = $request->get('id');
//        $em = $this->getDoctrine()->getManager();
//        $entity = $em->getRepository(Products::class)
//            ->find($id);
//        $entity->removeUpload();
//        $em->flush();
//
//        return new Response('ok', 200);
//    }

    /**
     * Creates a form to delete a Products entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('product_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', 'submit', [
                'label' => 'Ok',
                'attr' => [
                    'class' => 'btn btn-danger'
                ]])
            ->getForm()
            ;
    }
}
