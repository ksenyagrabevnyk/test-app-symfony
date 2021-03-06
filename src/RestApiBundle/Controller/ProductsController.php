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
//use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Version;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Validator\Mapping\CascadingStrategy;

class ProductsController extends FOSRestController
{
    /**
     *   Authorization user
     * ### REQUEST ###
     *      "name": "string",
     *      "password": "string",
     *      "device_token": "string",
     *      "firebase_token": "integer"
     *
     * ### RESPONSE ###
     * ### Status Code 200 and 201 ###
     *
     *     {
     *         "token": "string",
     *         "first_name": "string",
     *         "last_name": "string",
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
     *      {"name"="firebase_token", "dataType"="integer", "required"=false, "description"="firebase id for push notifications"},
     *      {"name"="name", "dataType"="string", "required"=false, "description"="User Name"},
     *      {"name"="password", "dataType"="string", "required"=false, "description"="User Password"},
     *      },
     *
     *  statusCodes={
     *      200="User login successfully",
     *      201="User creating successfully",
     *      400="Device Id not sent",
     *      404="If user with users token not found"
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
        $userName = $request->request->get('name');
        $userPassword = $request->request->get('password');
        $deviceToken = $request->request->get('device_token');
        $checkCurrentUser = $em->getRepository(Users::class)
            ->findOneBy([
                'username' => $userName,
                'password' => $userPassword
            ]);
        $response = [];

        if(!empty($checkCurrentUser)) {
            $userId = $checkCurrentUser->getId();
            $userInfo = $em->getRepository(Users::class)
                ->getUserInformation($userId);
            $response["token"] = $userInfo["uuid"];
            $response["first_name"] = $userInfo['firstName'];
            $response["second_name"] = $userInfo['secondName'];

            return new JsonResponse($response);

//        } elseif(empty($checkCurrentUser)) {
//
//            return "User not found, maybe you not registered";
        } else {

            return "User not found, maybe you not registered";
        }

    }

    /**
     *   Get info about products
     * ### REQUEST ###
     *
     *      "token": "string",
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
     *      {"name"="token", "dataType"="string", "format"="charset(32)", "required"=true, "description"="User uuid"},
     *     },
     *
     *  statusCodes={
     *      200="User login successfully",
     *      201="User creating successfully",
     *      400="Device Id not sent",
     *      404="If user with users token not found"
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
        $userUuid = $request->get('token');
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
     *          "token": "string",
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
     *      {"name"="token", "dataType"="string", "format"="charset(32)", "required"=true, "description"="Unique identificator user "},
     *      {"name"="products", "dataType"="json", "format"="json", "required"=true, "description"="Json with array products and count"},
     *     },
     *
     *  statusCodes={
     *      200="User login successfully",
     *      201="User creating successfully",
     *      400="Device Id not sent",
     *      404="If user with users token not found"
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
        $userUdid = $request->request->get('token');
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

            if ($checkProduct != null && $checkUser != null) {
                $orders = new Orders();
                $orders->setUserId($checkUser);
                $orders->setProductId($checkProduct);
                $orders->setCount($productCount);
                $currentTime = $currentDate = new \DateTime(
                    $request->query->get('start_date', 'now')
                );
                $orders->setPurchaseDate($currentTime);
                $em->persist($orders);
                $response['success'] = true;

            } else {

                $response['success'] = false;
            }
        }

        $em->flush();

        return $response;
    }
}
