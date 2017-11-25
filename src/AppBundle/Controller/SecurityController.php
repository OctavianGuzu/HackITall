<?php

namespace AppBundle\Controller;

use AppBundle\Form\LoginFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


class SecurityController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/security/login", name="security_login")
     */
    public function loginAction(Request $request)
    {
        $loginForm = $this->createForm(LoginFormType::class);

        $authUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
            'login_form' => $loginForm->createView()
        ));
    }
}