<?php

namespace AdminBundle\EntityRepository;

use Doctrine\ORM\EntityRepository;

class CategoriesRepository extends EntityRepository
{
    public function getCountAllActiveCategories()
    {
        $category = $this->createQueryBuilder('qb')
            ->select('COUNT(qb.id)')
            ->where('qb.isActive = true')
            ;

        return $category
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    public function getAllCategoriesQuery($search = null)
    {
        $category = $this->createQueryBuilder('qb')
            ->select('
                 qb
            ')
        ;
        if (!empty($search)) {
            $category
                ->where('qb.name LIKE :search')
                ->andWhere('qb.isActive = true')
                ->setParameter('search', "%{search}%")
                ->orderBy('qb.name')
                ;
        } else {
            $category
                ->andWhere('qb.isActive = true')
                ->orderBy('qb.name')
                ;
        }

        return $category
            ->getQuery()
            ->getArrayResult()
            ;
    }

    public function getCategoryInfo()
    {
        $category = $this->createQueryBuilder('qb')
            ->select('qb')
            ->where('qb.isActive = true')
        ;

        return $category
            ->getQuery()
            ->getArrayResult()
            ;
    }
}