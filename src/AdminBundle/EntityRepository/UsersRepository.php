<?php

namespace AdminBundle\EntityRepository;

use Doctrine\ORM\EntityRepository;

class UsersRepository extends EntityRepository
{
    public function getUserInformation($userId)
    {
        $user = $this->createQueryBuilder('qb')
            ->select(
                'qb.id,
                 qb.uuid, 
                 qb.username, 
                 qb.firstName,
                 qb.secondName
                 ')
            ->where('qb.enabled = true')
            ->andWhere('qb.id = :id')
            ->setParameter('id', $userId)
            ;

        return $user
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}