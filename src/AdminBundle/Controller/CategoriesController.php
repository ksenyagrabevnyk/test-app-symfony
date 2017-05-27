<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Categories;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AdminBundle\Form\CategoriesType;

/**
 * Categories controller
 *
 * @Route ("/categories")
 */
class CategoriesController extends Controller
{
    /**
     * Lists all Categories entities
     *
     * @Route("/", name="categories")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $countAllActiveCategories = $em->getRepository(Categories::class)
            ->getCountAllActiveCategories();
        $search = $request->get('search');
        $entities = $em->getRepository(Categories::class)
            ->getAllCategoriesQuery($search);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render(
            'AdminBundle:Categories:index.html.twig', [
                'entities' => $pagination,
                'count' => $countAllActiveCategories,
            ]
        );
    }

    /**
     * Create a new Categories
     *
     * @Route("/", name="category_create")
     * @Method("POST")
     */
     public function createAction(Request $request)
     {
         $em = $this->get('doctrine.orm.entity_manager');
         $entity = new Categories();
         $form = $this->createCreateForm($entity);
         $form->handleRequest($request);

         if ($form->isValid()) {
             $entity->setName($request->request->get('adminbundle_categories')['name']);
             $em->persist($entity);
             $em->flush();

             return $this->redirect($this->generateUrl('categories'));
         }

         return $this->render(
             'AdminBundle:Categories:new.html.twig',
             [
                 'entity' => $entity,
                 'form' => $form->createView(),
             ]
         );
     }

     /**
      * Creates a form to create a Category entity.
      *
      * @param Categories $entity The entity
      *
      * @return \Symfony\Component\Form\Form The form
      */
     private function createCreateForm(Categories $entity)
     {
         $form = $this->createForm(
             new CategoriesType(),
             null, [
                 'action' => $this->generateUrl('category_create'),
                 'method' => 'POST',
         ]);

         $form->add('submit', 'submit', [
             'label' => 'Create'
         ]);

         return $form;
     }

     /**
      * Displays a form to create a new Categories entity.
      *
      * @Route("/new", name="category_new")
      * @Method("GET")
      */
     public function newAction()
     {
         $entity = new Categories();
         $form = $this->createCreateForm($entity);

         return $this->render(
             'AdminBundle:Categories:new.html.twig', [
                 'entity' => $entity,
                 'form' => $form->createView(),
             ]);
     }

     /**
      * Displays a form to edit an existing Categories entity.
      *
      * @Route("/{id}/edit", name="category_edit")
      * @ParamConverter("entity", class="AdminBundle:Categories")
      * @Method("GET")
      */
     public function editAction(Categories $entity, $id)
     {
         if (!$entity) {
             throw $this->createNotFoundException('Unable to find Category entity');
         }

         $editForm = $this->createEditForm($entity);
         $deleteForm = $this->createDeleteForm($id);

         return $this->render(
           'AdminBundle:Categories:edit.html.twig', [
                 'entity' => $entity,
                 'edit_form' => $editForm->createView(),
                 'delete_form' => $deleteForm->createView(),
             ]
         );
     }

     /**
      * Creates a form to edit a Categories entity.
      *
      * @param Categories $entity The entity
      *
      * @return \Symfony\Component\Form\Form The form
      */
     private function createEditForm(Categories $entity)
     {
         $form = $this->createForm(
             new CategoriesType(),
             $entity, [
                 'action' => $this->generateUrl('category_update', [
                     'id' => $entity->getId(),
                     ]),
                 'method' => 'PUT',
             ]);

         $form->add('submit', 'submit', ['label' => 'Update']);

         return $form;
     }

    /**
     * Edits an existing Categories entity.
     *
     * @Route("/{id}", name="category_update", requirements={"id": "\d+"})
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $entity = $em->getRepository(Categories::class)
            ->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setName($request->request->get('adminbundle_categories')['name']);
            $em->flush();

            return $this->redirect($this->generateUrl('categories'));
        }

        return $this->render(
            'AdminBundle:Categories:edit.html.twig',
            [
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Deletes a Categories entity.
     *
     * @Route("/{id}", name="category_delete", requirements={"id": "\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $em = $this->get('doctrine.orm.entity_manager');
        $entity = $em->getRepository(Categories::class)
            ->find($id)
            ->setIsActive(0);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $em->flush();

        return $this->redirect($this->generateUrl('categories'));
    }

    /**
     * Creates a form to delete a Categories entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'category_delete', [
                        'id' => $id,
                    ])
            )
            ->setMethod('DELETE')
            ->add(
                'submit',
                'submit', [
                    'label' => 'Ok',
                    'attr' => [
                    'class' => 'btn btn-danger',
                    ],
                ])
            ->getForm();
    }
}