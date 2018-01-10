<?php

namespace Appstore\Bundle\MedicineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('MedicineBundle:Default:index.html.twig', array('name' => $name));
    }
}
