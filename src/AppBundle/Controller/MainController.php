<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Request;

class MainController extends Controller
{
    public function homepageAction()
    {
        return $this->render('login.html');
    }
}