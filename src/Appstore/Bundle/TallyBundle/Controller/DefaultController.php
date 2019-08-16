<?php

namespace Appstore\Bundle\TallyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('TallyBundle:Default:index.html.twig', array('name' => $name));
    }
}
