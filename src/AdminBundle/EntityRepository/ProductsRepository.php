<?php

namespace AdminBundle\EntityRepository;

use Doctrine\ORM\EntityRepository;

class ProductsRepository extends EntityRepository
{
    public function getCountAllActiveProducts()
    {
        $products = $this->createQueryBuilder('qb')
            ->select('COUNT(qb.id)')
            ->where('qb.isActive = true')
            ;

        return $products
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    public function getAllProductsQuery($search = null)
    {
        $products = $this->createQueryBuilder('qb')
            ->select('
                 qb
            ')
        ;
        if (!empty($search)) {
            $products
                ->where('qb.name LIKE :search')
                ->andWhere('qb.isActive = true')
                ->setParameter('search', "%{search}%")
                ->orderBy('qb.name')
                ;
        } else {
            $products
                ->andWhere('qb.isActive = true')
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
                qb.salePrice,
                c.id AS category_id, 
                c.name AS category_name
            ')
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