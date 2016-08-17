<?php

namespace Xiidea\Bundle\DomainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('XiideaDomainBundle::index.html.twig', array('name' => 'mobile'));
    }

    public function educationHomeAction()
    {
        return $this->render('XiideaDomainBundle::index.html.twig', array('name' => 'Education'));
    }

    public function educationAppHomeAction($subdomain = "")
    {
        return $this->render('XiideaDomainBundle::index.html.twig', array('name' => "Education sub ". $subdomain));
    }

    public function educationAppMobileHomeAction($subdomain = "")
    {
        return $this->render('XiideaDomainBundle::index.html.twig', array('name' => "Education sub mobile ". $subdomain));
    }

    public function ecomHomeAction()
    {
        return $this->render('XiideaDomainBundle::index.html.twig', array('name' => 'Ecom'));
    }

    public function ecomAppHomeAction($subdomain = "")
    {
        return $this->render('XiideaDomainBundle::index.html.twig', array('name' => "Ecom sub ". $subdomain));
    }


    public function errorAction()
    {
        return $this->render('XiideaDomainBundle::index.html.twig', array('name' => 'error'));
    }
}
