<?php

namespace AdminBundle\EntityRepository;

use Doctrine\ODM\PHPCR\Query\Query;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;


class OrdersRepository extends EntityRepository
{
    /**
     * @param $userId
     * @return array
     */
    public function getOrdersInfoForUser($userId)
    {
        $orders = $this->createQueryBuilder('qb')
            ->select('
                qb.id AS orderId,
                qb.count,
                qb.purchaseDate,
                p.name AS productName,
                p.salePrice,
                p.purchasePrice,
                p.profit,
                u.firstName,
                u.secondName,
                u.thirdName
            ')
            ->leftJoin('qb.productId', 'p')
//            ->leftJoin('p.categoryId', 'c')
            ->leftJoin('qb.userId', 'u')
            ->where('p.isActive = true')
            ->andWhere('u.id = :id')
            ->setParameter('id', $userId);

        return $orders
            ->getQuery()
            ->getResult();
    }


    /**
     * @param $yesterday
     * @return array
     */
    public function getOrdersByDateQuery($userId, $yesterday = null, $today = null)
    {
        $orders = $this->createQueryBuilder('qb')
            ->select('
                qb.id AS orderId,
                qb.count,
                qb.purchaseDate,
                p.name AS productName,
                p.salePrice,
                p.purchasePrice,
                p.profit,
                u.firstName,
                u.secondName,
                u.thirdName
            ')
            ->leftJoin('qb.productId', 'p')
//            ->leftJoin('p.categoryId', 'c')
            ->leftJoin('qb.userId', 'u')
            ->where('p.isActive = true')
            ->andWhere('u.id = :id')
            ->setParameter('id', $userId);
        ;
            if ($yesterday !== null) {
                $orders->andWhere("qb.purchaseDate = :yesterday")
                  ->setParameter('yesterday', $yesterday);
            }

            if ($today !== null) {
//                var_dump($today); die();
                $orders->andWhere("qb.purchaseDate = :today")
                    ->setParameter('today', $today);
            }
//            ->andWhere('qb.purchaseDate <= :date')
//            ->setParameter('date', $date);

        return $orders
            ->getQuery()
            ->getResult();
    }

}