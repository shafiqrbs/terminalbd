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
        $data = array(
            'resource' => '@FrontendBundle/Resources/config/routing/webservice.yml',
            'domain' => 'www.tlsbd.org',
            'subdomain' =>'tlsbd'
        );



        $routes = array(
            '_domain_app_' . strtolower(str_replace('.', '_', $data['domain'])) => array(
                'prefix' => '/',
                'resource' => $data['resource'],
                'domain' => $data['domain'],
                'defaults' => array(
                    'subdomain' =>$data['subdomain']
                )
            )
        );

        $routesString = Yaml::dump($routes);

        file_put_contents(realpath(WEB_PATH . "../app/config/dynamic/sites.yml"), $routesString);

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
