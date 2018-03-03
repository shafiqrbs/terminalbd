<?php

namespace Appstore\Bundle\EducationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('EducationBundle:Default:index.html.twig', array('name' => $name));
    }
}
