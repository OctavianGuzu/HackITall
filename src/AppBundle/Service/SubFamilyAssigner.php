<?php
/**
 * Created by PhpStorm.
 * User: constantin.andreescu
 * Date: 7/21/2017
 * Time: 2:11 PM
 */

namespace AppBundle\Service;

use AppBundle\Entity\SubFamily;
use Doctrine\Bundle\DoctrineBundle\Registry;

class SubFamilyAssigner
{

    private $doctrine;

    /**
     * @param Registry $doctrine
     */
    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function assignSubFamily()
    {
        /** @var Registry $reg */
        $reg = $this->doctrine;
        $entries = $reg->getEntityManager()->getRepository('AppBundle:SubFamily')->findAll();
        $nrOfEntries = count($entries);
        $i = rand(1, 84);
        return $reg->getEntityManager()->getRepository('AppBundle:SubFamily')->findOneBy(
            [
                'id' => $i
            ]
        );
    }
}