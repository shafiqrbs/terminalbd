<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('AppstoreInventoryBundle:Default:index.html.twig', array('name' => $name));
    }
}
