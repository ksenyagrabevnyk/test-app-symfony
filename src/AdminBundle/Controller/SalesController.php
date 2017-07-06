<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Categories;
use AdminBundle\Entity\Users;
use AdminBundle\Entity\Orders;
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
 * Sales controller
 *
 * @Route ("/sales")
 */
class SalesController extends Controller
{
    /**
     * Sales controller
     *
     * @Route("/", name="sales")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $user =  $this->get('security.context')->getToken()->getUser();
        $orderInfo = $em->getRepository(Orders::class)
            ->getOrdersInfoForUser($user->getId());
        $groupItem = [];

        foreach ($orderInfo as $key => $item) {

            $groupItem[$item['purchaseDate']->format('Y-m-d')]['date'] = $item['purchaseDate']->format('Y-m-d');
            $groupItem[$item['purchaseDate']->format('Y-m-d')]['items'][] = $item;

            if (count($groupItem[$item['purchaseDate']->format('Y-m-d')]['items']) === 1 ) {
                $groupItem[$item['purchaseDate']->format('Y-m-d')]['count'] = $item['count'];
                $groupItem[$item['purchaseDate']->format('Y-m-d')]['salePrice'] = $item['salePrice'];

            } else {
                $groupItem[$item['purchaseDate']->format('Y-m-d')]['count'] += $item['count'];
                $groupItem[$item['purchaseDate']->format('Y-m-d')]['salePrice'] += $item['salePrice'] * $item['count'];
            }

            if(!isset($groupItem[$item['purchaseDate']->format('Y-m-d')]['total_price'])) {
                $groupItem[$item['purchaseDate']->format('Y-m-d')]['total_price'] = 0;
                $groupItem[$item['purchaseDate']->format('Y-m-d')]['total_price_with_sale'] = 0;
            }

            if($item['salePrice'] != null) {
                $groupItem[$item['purchaseDate']->format('Y-m-d')]['total_price'] += $item['purchasePrice'] * $item['count'];
                $groupItem[$item['purchaseDate']->format('Y-m-d')]['total_price_with_sale'] += ($item['purchasePrice'] - $item['salePrice']) * $item['count'];
//           echo '<pre>';
//            var_dump($groupItem); die();
            } else {
                $groupItem[$item['purchaseDate']->format('Y-m-d')]['total_price'] += $item['purchasePrice'] * $item['count'];
                $groupItem[$item['purchaseDate']->format('Y-m-d')]['total_price_with_sale'] = $groupItem[$item['purchaseDate']->format('Y-m-d')]['total_price'];
            }
        }


//            echo "<pre>";
//            print_r(($groupItem)); die;
        return $this->render(
            'AdminBundle:Sales:index.html.twig', [
             'entities' => array_values($groupItem)
            ]
        );

    }

    /**
     * Filter sales controller
     *
     * @Route("/filter", name="filter")
     * @Method("GET")
     */
    public function filterAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $user =  $this->get('security.context')->getToken()->getUser();
        $userId = $user->getId();
        $currentDate = new \DateTime(
            $request->query->get('start_date', 'now')
        );
        $yesterday = new \DateTime(
            $request->query->get('start_date', 'last day')
        );
        $thisWeek = new \DateTime(
            $request->query->get('start_date', 'last week')
        );
        $thisMonth = new \DateTime(
            $request->query->get('start_date', 'last month')
        );

        if (isset($_GET['yesterday'])) {
            $yesterday = $yesterday->format('Y-m-d');

            $ordersByDates = $em->getRepository(Orders::class)
                ->getOrdersByDateQuery($userId, $yesterday, $today = null, $thisWeek = null);

            echo '<pre>';
            print_r($ordersByDates); die();

        } elseif (isset($_GET['today'])) {

            $today = $currentDate->format('Y-m-d');
            $ordersByDates = $em->getRepository(Orders::class)
                ->getOrdersByDateQuery($userId, $yesterday = null, $today, $thisWeek = null);

           echo '<pre>';
            print_r($ordersByDates) ; die;

        } elseif (isset($_GET['this-is-week'])) {
            $thisWeek = $thisWeek->format('Y-m-d');
            $today = $currentDate->format('Y-m-d');
            $ordersByDates = $em->getRepository(Orders::class)
                ->getOrdersByDateQuery($userId, $yesterday = null, $today, $thisWeek);

            echo '<pre>';
            print_r($ordersByDates); die;
//            var_dump($today, $thisWeek) ; die;

        } elseif (isset($_GET['this-is-month'])) {

            var_dump('this-is-month') ; die;
        }
    }

}