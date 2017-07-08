<?php

namespace AdminBundle\EntityRepository;

use AdminBundle\Entity\Users;
use Doctrine\ORM\EntityRepository;

class ProductsRepository extends EntityRepository
{
    public function getCountAllActiveProducts(Users $user)
    {
        $products = $this->createQueryBuilder('qb')
            ->select('COUNT(qb.id)')
            ->leftJoin('qb.categoryId', 'c')
            ->leftJoin('c.userId', 'u')
            ->where('qb.isActive = true')
            ->andWhere('u.id = :id')
            ->setParameter('id', $user)
            ;

        return $products
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    public function getAllProductsQuery(Users $user, $search = null)
    {
        $products = $this->createQueryBuilder('qb')
            ->select('
                 qb
            ')
            ->leftJoin('qb.categoryId', 'c')
            ->leftJoin('c.userId', 'u')
        ;
        if (!empty($search)) {
            $products
                ->where('qb.name LIKE :search')
                ->andWhere('qb.isActive = true')
                ->andWhere('u.id = :id')
                ->setParameter('id', $user)
                ->setParameter('search', "%{search}%")
                ->orderBy('qb.name')
                ;
        } else {
            $products
                ->andWhere('qb.isActive = true')
                ->andWhere('u.id = :id')
                ->setParameter('id', $user)
                ->orderBy('qb.name')
                ;
        }

        return $products
            ->getQuery()
            ;
    }

    public function getGenerationDataProducts($categoryId)
    {
        $products = $this->createQueryBuilder('qb')
            ->select('
                qb.id AS product_id,
                qb.name AS product_name,
                qb.salePrice AS sale_price,
                qb.salePrice AS purchase_price,
                qb.profit AS profit,
                qb.imgPath AS img_path'

//                c.id AS category_id,
//                c.name AS category_name
            )
            ->leftJoin('qb.categoryId', 'c')
            ->where('qb.isActive = true')
            ->andWhere('c.isActive = true')
            ->andWhere('c.id = :categoryId')
            ->setParameter('categoryId', $categoryId)
        ;


        return $products
            ->getQuery()
            ->getArrayResult();
    }
}