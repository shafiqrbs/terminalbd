<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    public function indexAction($name)
    {
        exit;
        return $this->render('HospitalBundle:Default:index.html.twig', array('name' => $name));
    }
}
