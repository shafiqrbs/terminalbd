<?php

namespace Appstore\Bundle\CustomerBundle\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\EcommerceBundle\Form\OrderType;
use Frontend\FrontentBundle\Service\Cart;
use Setting\Bundle\ToolBundle\Entity\Module;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends Controller
{


    public function paginate($entities)
    {
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        return $pagination;
    }

    public function indexAction($shop)
    {
        
        $user = $this->getUser();
        if(!empty($shop)){
            $globalOption = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('slug' => $shop));
        }else{
            $globalOption ='';
        }

        $domainType =  $globalOption->getDomainType();
        $domain = !empty($domainType) ? $domainType : "dashboard";
        return $this->render("CustomerBundle:Customer:dashboard.html.twig", array(
            'user'         => $user,
            'globalOption' => $globalOption,
        ));

    }

    public function customerDomainAction()
    {

        $user = $this->getUser();
        return $this->render('CustomerBundle:Customer:domain.html.twig', array(
            'user' => $user,
            'globalOption' => '',
        ));

    }
    
    public function moduleAction(Module $module)
    {

        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->findModuleContentList($globalOption,$module->getId());
        $pagination = $this->paginate($entities);
        return $this->render('CustomerBundle:Customer:module.html.twig', array(
            'globalOption' => $globalOption,
            'module' => $module,
            'pagination' => $pagination,
        ));

    }
    public function moduleDetailsAction()
    {
        $user = $this->getUser();
        return $this->render('CustomerBundle:Customer:domain.html.twig', array(
            'user' => $user,
            'globalOption' => '',
        ));

    }



}
