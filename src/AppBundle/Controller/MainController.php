<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Search;
use AppBundle\Repository\SearchRepository;
use Faker\Provider\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class MainController extends Controller
{

    /**
     * @Route("/", name="root")
     */
    public function rootAction()
    {
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/home", name="home")
     */
    public function homeAction()
    {
        return $this->render('home.html.twig');
    }

    /**
     * @Route("/register", name="register")
     */
    public function registerAction()
    {
        return $this->render('register.html.twig');
    }

    /**
     * @Route("/register-ajax", name="register-ajax")
     */
    public function registerAjaxAction()
    {
        if(isset($_POST['username']) && isset($_POST['password']) &&
            isset($_POST['home']) && isset($_POST['work'])) {
            try {
                $user = new User();
                $user->setName($_POST['username']);
                $plainPassword = $_POST['password'];
                $encoder = new BCryptPasswordEncoder(4);
                $encoded = $encoder->encodePassword($plainPassword, $user->getSalt());
                $user->setPassword($encoded);
                $user->setHome($_POST['home']);
                $user->setWork($_POST['work']);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                return new JsonResponse(["result" => true]);
            } catch (Exception $e) {
                var_dump($e->getMessage());
            }
            return new JsonResponse(["result" => true]);
        } else {
            return new JsonResponse(["result" => false]);
        }
    }

    /**
     * @Route("/saveSearch-ajax", name="saveSearch-ajax")
     */
    public function saveSearchAjaxAction()
    {
        if(isset($_POST['name'])) {
            try {
                $search = new Search();
                $search->setName($_POST['name']);
                $search->setDateCreated(new \DateTime());
                $search->setUser($this->get('security.token_storage')->getToken()->getUser());
                $em = $this->getDoctrine()->getManager();
                $em->persist($search);
                $em->flush();
                return new JsonResponse(["result" => true]);
            } catch (Exception $e) {
                var_dump($e->getMessage());
            }
            return new JsonResponse(["result" => true]);
        } else {
            return new JsonResponse(["result" => false]);
        }
    }

    /**
     * @Route("/getHome-ajax", name="getHome-ajax")
     */
    public function getHomeAjaxAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $home = $user->getHome();
        return new JsonResponse(["home" => $home]);
    }

    /**
     * @Route("/getWork-ajax", name="getWork-ajax")
     */
    public function getWorkAjaxAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $work = $user->getWork();
        return new JsonResponse(["work" => $work]);
    }

    /**
     * @Route("/getHistory-ajax", name="getHistory-ajax")
     */
    public function getHistoryAjaxAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userId = $user->getId();
        $repo = $this->getDoctrine()->getRepository('AppBundle:Search');
        /** @var Search[] $history */
        $history = $repo->findBy(["user" => $user]);
        $result = [];
        foreach ($history as $entry) {
            $e = [];
            $e['name'] =  $entry->getName();
            $e['date_created'] =  $entry->getDateCreated();
            $result[] = $e;
        }

        return new JsonResponse(["history" => $result, "username" => $user->getUsername()]);
    }

}