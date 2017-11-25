<?php

namespace AppBundle\Repository;

use AppBundle\Entity\SubFamily;
use Doctrine\ORM\EntityRepository;

class SubFamilyRepository extends EntityRepository
{
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    function findAllAlphabeticalReversed()
    {
        return $this->createQueryBuilder('sub_family')
                ->orderBy('sub_family.name', 'DESC');

    }
}