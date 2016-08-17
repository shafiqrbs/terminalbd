<?php

namespace Appstore\Bundle\ImsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ImsBundle:Default:index.html.twig', array('name' => $name));
    }
}
