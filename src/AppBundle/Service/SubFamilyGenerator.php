<?php
/**
 * Created by PhpStorm.
 * User: constantin.andreescu
 * Date: 7/21/2017
 * Time: 2:52 PM
 */

namespace AppBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use AppBundle\Entity\SubFamily;

class SubFamilyGenerator
{
    /** @var  Registry */
    private $doctrine;

    /**
     * @param Registry $doctrine
     */
    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function generateSubFamilies($url, $needle)
    {
        $content = file_get_contents(strip_tags($url));
        $lastPos = 0;
        while (($lastPos = strpos($content, $needle, $lastPos))!== false) {
            $positions[] = $lastPos;
            $lastPos = $lastPos + strlen($needle);
        }
        foreach ($positions as $position) {
            $specie = '';
            $i = $position + strlen($needle) + 1;
            while ($content[$i] != ' ') {
                $specie .= $content[$i];
                $i++;
            }
            if (ctype_alpha($specie)) {
                $species[] = $specie;
            }
        }
        $em = $this->doctrine->getEntityManager();
        foreach ($species as $specie) {
            $fam = new SubFamily();
            $fam->setName($specie);
            $em->persist($fam);
            $em->flush();
        }
    }
}