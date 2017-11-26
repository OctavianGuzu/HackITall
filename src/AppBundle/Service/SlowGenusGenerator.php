<?php
/**
 * Created by PhpStorm.
 * User: constantin.andreescu
 * Date: 7/25/2017
 * Time: 5:34 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\Genus;
use Doctrine\Bundle\DoctrineBundle\Registry;

class SlowGenusGenerator
{

    private $doctrine;

    /**
     * @param Registry $doctrine
     */
    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function generateOneGenus()
    {
        /** @var Registry $reg */
        $reg = $this->doctrine;
        $chosenGenus = new Genus();
        sleep(5);
        //for ($i=0; $i<10000; $i++) {

            /** @var Genus $chosenGenus */
            $chosenGenus = $reg->getRepository('AppBundle:Genus')->findOneBy([
                'id' => 1
            ]);

        return $chosenGenus;
    }
}