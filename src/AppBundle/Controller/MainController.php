<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class MainController extends Controller
{

    /**
     * @Route("/home", name="home")
     */
    public function homeAction()
    {
        return $this->render('home.html.twig');
    }


    public function registerAction()
    {
        // whatever *your* User object is
        $user = new User();
        $user->setName("Maria");
        $plainPassword = 'Ioana';
        $encoder = new BCryptPasswordEncoder(4);
        $encoded = $encoder->encodePassword($plainPassword, $user->getSalt());

        $user->setPassword($encoded);
        $manager =$this->getDoctrine()->getManager();
        $manager->persist($user);
        $manager->flush();
        return new Response("succes");

    }
}