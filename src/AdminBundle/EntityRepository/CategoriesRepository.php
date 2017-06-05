<?php

namespace AdminBundle\EntityRepository;

use Doctrine\ORM\EntityRepository;

class CategoriesRepository extends EntityRepository
{
    public function getCountAllActiveCategories($userId)
    {
        $category = $this->createQueryBuilder('qb')
            ->select('COUNT(qb.id)')
            ->leftJoin('qb.userId', 'u')
            ->where('qb.isActive = true')
            ->andWhere('u.id = :id')
            ->setParameter('id', $userId)
        ;

        return $category
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    public function getAllCategoriesQuery($search = null, $userId)
    {
        $category = $this->createQueryBuilder('qb')
            ->select('
                 qb
            ')
        ;
        if (!empty($search)) {
            $category
                ->leftJoin('qb.userId', 'u')
                ->where('qb.name LIKE :search')
                ->andWhere('qb.isActive = true')
                ->andWhere('u.id = :id')
                ->setParameter('search', "%{search}%")
                ->setParameter('id', $userId)
                ->orderBy('qb.name')
                ;
        } else {
            $category
                ->leftJoin('qb.userId', 'u')
                ->andWhere('qb.isActive = true')
                ->andWhere('u.id = :id')
                ->setParameter('id', $userId)
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
            ->leftJoin('qb.userId', 'u')
            ->where('qb.isActive = true')
//            ->andWhere('u.id = :id')
//            ->setParameter('id', $userId)
        ;

        return $category
            ->getQuery()
            ->getArrayResult()
            ;
    }
}