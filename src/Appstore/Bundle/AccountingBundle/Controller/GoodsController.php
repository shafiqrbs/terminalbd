<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GoodsController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('AccountingBundle:Default:index.html.twig', array('name' => $name));
    }
}
