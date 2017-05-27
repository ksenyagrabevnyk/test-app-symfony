<?php

namespace RestApiBundle\Controller;

use AdminBundle\Entity\Categories;
use AdminBundle\Entity\Products;
use AdminBundle\Helper\GenerationUUIDHelper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Acl\Exception\Exception;
use Symfony\Component\Debug\Exception\ClassNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Version;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\Mapping\CascadingStrategy;

class ProductsController extends FOSRestController
{
    /**
     *   Authorization user and get info about products
     * ### REQUEST ###
     *
     * ### RESPONSE ###
     * ### Status Code 200 and 201 ###
     *     [
     *       {
     *          "products": [
     *                        {
     *                          "category_name": [
     *                                        {
     *                                          "product_name": "string",
     *                                          "sale_price": "float",
     *                                          "purchase_price": "float",
     *                                          "profit": "float",
     *                                          "img_path": "string",
     *                                        }
     *                                     ],
     *                         }
     *                      ],
     *        }
     *      ]
     *
     * ### Status Code 400 and 404 ###
     *
     *     {
     *       "error": {
     *           "code": "integer",
     *           "message": "String",
     *       }
     *     }
     *
     * @Rest\Post("/auth")
     * @ApiDoc(
     *  description="Login users and get info product",
     *  section="User",
     *  resource=true,
     *  requirements={
     *      {
     *          "name"="_format",
     *          "dataType"="string",
     *          "requirement"="json",
     *      }
     *  },
     *  parameters={
     *      {"name"="products", "dataType"="json", "required"=true, "description"="Products info"},
     *     },
     *
     *  statusCodes={
     *      200="User login successfully",
     *      201="User creating successfully",
     *      400="Device Id not sent",
     *      404="If user with user_uuid not found"
     *  },
     *     tags={
     *         "done"
     *     }
     * )
     *
     */
    public function authAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $categories = $em->getRepository('AdminBundle:Categories')->getCategoryInfo();
        $products = [];

        foreach ($categories as $category) {
            $categoryId = $category["id"];
            $products[$category["name"]] = $em->getRepository('AdminBundle:Products')->getGenerationDataProducts($categoryId);
        }


//        $response = [];
//
//        $response['delete'] = [
//            'theme' => $getDeletedEntityUuidByEntityTypeService
//                ->getDeletedEntityUuidByEntityType($userId, 'theme', 'delete', false),
//            'card' => $getDeletedEntityUuidByEntityTypeService
//                ->getDeletedEntityUuidByEntityType($userId, 'card', 'delete', false),
//            'phrases' => $getDeletedEntityUuidByEntityTypeService
//                ->getDeletedEntityUuidByEntityType($userId, 'phrases', 'delete', false),
//            'files' => $getDeletedEntityUuidByEntityTypeService
//                ->getDeletedEntityUuidByEntityType($userId, 'files', 'delete', false),
//        ];
        return new JsonResponse([
            $products

        ], 200);
//       var_dump($products); die;
    }
}
