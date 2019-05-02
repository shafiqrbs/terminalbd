<?php

namespace Appstore\Bundle\CustomerBundle\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\EcommerceBundle\Form\OrderType;
use Frontend\FrontentBundle\Service\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends Controller
{


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

        return $this->render("CustomerBundle:Customer:{$domain}.html.twig", array(
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
    
}
