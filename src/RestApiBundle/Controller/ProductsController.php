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
     *   Authorization user
     * ### REQUEST ###
     *
     *     "udid": "string",
     *     "user_uuid": "string",
     *     "social_token": "string",
     *     "social_type":  "string",
     *     "social_user_id": "string",
     *     "device_token": "string",
     *     "time_stamp": "float",
     *     "cards": [
     *                 {
     *                    "card_uuid": "string",
     *                     "score": "float"
     *                 }
     *           ]
     *
     * ### RESPONSE ###
     * ### Status Code 200 and 201 ###
     *     {
     *       "user_uuid": "string",
     *       "cards": [
     *          {
     *             "card_uuid": "string",
     *             "friends_count": "integer",
     *             "users": [
     *                 {
     *                   "user_uuid": "string",
     *                   "score": "float",
     *                   "name": "string",
     *                   "photo_path": "string",
     *                 }
     *               ]
     *            }
     *         ],
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
     *  description="Login via facebook or vk or anonymously",
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
     *      {"name"="udid", "dataType"="string", "required"=false, "description"="User udid"},
     *      {"name"="social_token", "dataType"="string", "format"="vk token| fb token", "required"=false, "description"="token connection"},
     *      {"name"="social_type", "dataType"="string", "format"="vk | fb", "required"=false, "description"="Social type"},
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
