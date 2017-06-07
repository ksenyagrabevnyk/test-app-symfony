<?php

namespace RestApiBundle\Controller;

use AdminBundle\Entity\Categories;
use AdminBundle\Entity\Orders;
use AdminBundle\Entity\Products;
use AdminBundle\Entity\Users;
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
     *   Authorization user
     * ### REQUEST ###
     *
     *      "device_token": "string",
     *      "firebase_id": "integer"
     *
     * ### RESPONSE ###
     * ### Status Code 200 and 201 ###
     *
     *     {
     *         "user_uuid": "string",
     *         "user_username": "string",
     *         "user_first_name": "string",
     *      }
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
     *  description="Login users",
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
     *      {"name"="device_token", "dataType"="string", "format"="charset(32)", "required"=false, "description"="Device token"},
     *      {"name"="firebase_id", "dataType"="integer", "required"=false, "description"="firebase id for push notifications"},
     *    },
     *
     *  statusCodes={
     *      200="User login successfully",
     *      201="User creating successfully",
     *      400="Device Id not sent",
     *      404="If user with user_uuid not found"
     *  },
     *     tags={
     *         "ready for mob testing"
     *     }
     * )
     *
     */
    public function authAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $user =  $this->get('security.context')->getToken()->getUser();
        $userId = $user->getId();
//        $deviceToken = $request->request->get('device_token');
//        $categories = $em->getRepository('AdminBundle:Categories')->getCategoryInfo();
        $response = [];
        $userInfo = $em->getRepository(Users::class)
            ->getUserInformation($userId);
        $response["user_uuid"] = $userInfo["uuid"];
        $response["user_id"] = $userInfo["id"];
        $response["user_username"] = $userInfo['username'];
        $response["user_first_name"] = $userInfo['firstName'];
        $response["user_second_name"] = $userInfo['secondName'];
//        foreach ($categories as $category) {
//            $categoryId = $category["id"];
//            $response[$category["name"]] = $em->getRepository('AdminBundle:Products')->getGenerationDataProducts($categoryId);
//        }

        return new JsonResponse($response);
    }

    /**
     *   Get info about products
     * ### REQUEST ###
     *
     *      "user_uuid": "string",
     *
     *
     * ### RESPONSE ###
     * ### Status Code 200 and 201 ###
     *       {
     *          "categories": [
     *               {
     *                  "category_name": "string",
     *                  "category_id": "integer",
     *                  "products": [
     *                        {
     *                           "product_id": "integer",
     *                           "product_name": "string",
     *                           "sale_price": "float",
     *                           "purchase_price": "float",
     *                           "profit": "float",
     *                           "img_path": "string",
     *                         }
     *                    ],
     *                 }
     *             ],
     *          }
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
     * @Rest\Post("/get-products")
     * @ApiDoc(
     *  description="Get info product",
     *  section="Products",
     *  resource=true,
     *  requirements={
     *      {
     *          "name"="_format",
     *          "dataType"="string",
     *          "requirement"="json",
     *      }
     *  },
     *  parameters={
     *      {"name"="user_uuid", "dataType"="string", "format"="charset(32)", "required"=true, "description"="User uuid"},
     *     },
     *
     *  statusCodes={
     *      200="User login successfully",
     *      201="User creating successfully",
     *      400="Device Id not sent",
     *      404="If user with user_uuid not found"
     *  },
     *     tags={
     *         "ready for mob testing"
     *     }
     * )
     *
     */
    public function getProductsAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $userUuid = $request->get('user_uuid');
        $userId = $em->getRepository(Users::class)
            ->findOneBy([
                'uuid' => $userUuid
            ])
            ->getId();
        $categories = $em->getRepository('AdminBundle:Categories')->getCategoryInfo($userId);
        $response['categories'] = [];

        foreach ($categories as $category) {
            $categoryId = $category["id"];
            $response['categories'][] = [
                'category_name' => $category["name"],
                'category_id' => $category["id"],
                'products' => $em->getRepository('AdminBundle:Products')->getGenerationDataProducts($categoryId)
            ];
        }

        return new JsonResponse($response);
    }

    /**
     *   Buying products
     *
     * ### REQUEST ###
     *
     *          "user_uuid": "string",
     *          "products": [
     *                {
     *                  "product_id": "integer",
     *                  "count": "integer",
     *                }
     *           ]
     *
     * ### RESPONSE ###
     * ### Status Code 200 and 201 ###
     *
     *          "success": "boolean";
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
     * @Rest\Post("/buy")
     * @ApiDoc(
     *  description="Buying products and save to db",
     *  section="Buying",
     *  resource=true,
     *  requirements={
     *      {
     *          "name"="_format",
     *          "dataType"="string",
     *          "requirement"="json",
     *      }
     *  },
     *  parameters={
     *      {"name"="user_uuid", "dataType"="string", "format"="charset(32)", "required"=true, "description"="Unique identificator user "},
     *      {"name"="products", "dataType"="json", "format"="json", "required"=true, "description"="Json with array products and count"},
     *     },
     *
     *  statusCodes={
     *      200="User login successfully",
     *      201="User creating successfully",
     *      400="Device Id not sent",
     *      404="If user with user_uuid not found"
     *  },
     *     tags={
     *         "ready for mob testing"
     *     }
     * )
     *
     */
    public function buyingAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $userUdid = $request->request->get('user_uuid');
        $products = json_decode($request->request->get('products'), true);

        foreach ($products as $product) {
            $productId = $product['product_id'];
            $productCount = $product['count'];
            $checkProduct = $em->getRepository(Products::class)
                ->findOneBy([
                    'id' => $productId
                ]);
            $checkUser = $em->getRepository(Users::class)
                ->findOneBy([
                    'uuid' => $userUdid
                ]);

//            var_dump($checkUser->getId()); die;
            if ($checkProduct != null && $checkUser != null) {
                $userId = $checkProduct->getId();
                $orders = new Orders();
                $orders->setUserId($checkUser);
                $orders->setProductId($checkProduct);
                $orders->setCount($productCount);
                $currentTime = getdate()[0];
                $orders->setPurchaseDate($currentTime);
                $em->persist($orders);
                $em->flush();

                return true;
            }

            return false;
        }
    }
}
