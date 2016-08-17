<?php

namespace Appstore\Bundle\ConfigBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ConfigBundle:Default:index.html.twig', array('name' => $name));
    }
}
