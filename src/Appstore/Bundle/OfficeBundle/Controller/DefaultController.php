<?php

namespace Appstore\Bundle\OfficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('OfficeBundle:Default:index.html.twig', array('name' => $name));
    }
}
