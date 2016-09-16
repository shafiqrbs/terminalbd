<?php

namespace Appstore\Bundle\CustomerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CustomerBundle:Default:index.html.twig', array('name' => $name));
    }
}
