<?php

namespace Syndicate\Bundle\ComponentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SyndicateComponentBundle:Default:index.html.twig', array('name' => $name));
    }
}
