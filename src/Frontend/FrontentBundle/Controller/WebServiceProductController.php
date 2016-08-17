<?php

namespace Frontend\FrontentBundle\Controller;

use Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem;
use Desktop\Bundle\Service\MobileDetect;
use Moltin\Cart\Cart;
use Moltin\Cart\Identifier\Cookie;
use Moltin\Cart\Storage\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class WebServiceProductController extends Controller
{

    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
           10  /*limit per page*/
        );
        $pagination->setTemplate('FrontendBundle:Template/Mobile:mobilePagination.html.twig');
        return $pagination;
    }

   public function productAction($subdomain)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));

        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            $data = $_REQUEST;
            $inventory = $globalOption->getInventoryConfig();
            $entities = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseVendorItem')->findWithSearch($inventory,$data);
            $pagination = $this->paginate($entities);

            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if( ! $detect->isMobile() && ! $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }
            $categoryTree = $this->getDoctrine()->getRepository('InventoryBundle:Product')->getProductCategories($globalOption->getInventoryConfig());
            return $this->render('FrontendBundle:'.$theme.':product.html.twig',
                array(

                    'globalOption'  => $globalOption,
                    'categoryTree'  => $categoryTree,
                    'products'    => $pagination,
                )
            );
        }
    }

    public function productDetailsAction($subdomain,PurchaseVendorItem $item)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        $products ='';
        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            $inventory = $globalOption->getInventoryConfig();

            /*==========Related Product===============================*/

            if(!empty($item->getMasterItem())){

                $cat = $item->getMasterItem()->getCategory()->getId();
                $data = array('cat' => $cat);
                $entities = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseVendorItem')->findWithSearch($inventory,$data);
                $products = $this->paginate($entities);
            }

            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if(! $detect->isMobile() && ! $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }
            return $this->render('FrontendBundle:'.$theme.':productDetails.html.twig',
                array(
                    'globalOption'    => $globalOption,
                    'product'    => $item,
                    'products'    => $products,


                )
            );
        }
    }

    public function productAddCartAction($subdomain,Item $item)
    {
        $data ='';
        $cart = new Cart(new Session(), new Cookie());
        $cart->insert(array(
            'id'       => $item->getId(),
            'name'     => $item->getMasterItem()->getName(),
            'price'    => $item->getWebPrice(),
            'quantity' => 1
        ));
        var_dump($cart->contents());
        exit;
    }


}
