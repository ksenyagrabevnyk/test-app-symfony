<?php

namespace AdminBundle\EntityRepository;

use Doctrine\ORM\EntityRepository;

class UsersRepository extends EntityRepository
{
    public function getUserInformation($deviceToken)
    {
        $user = $this->createQueryBuilder('qb')
            ->select(
                'qb.uuid, 
                 qb.username, 
                 qb.firstName
                 ')
            ->where('qb.enabled = true')
            ->andWhere('qb.deviceToken = :deviceToken')
            ->setParameter('deviceToken', $deviceToken)
            ;

        return $user
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}