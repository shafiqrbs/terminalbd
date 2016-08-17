<?php

namespace Desktop\Bundle\Controller;

use Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem;
use Moltin\Cart\Cart;
use Moltin\Cart\Identifier\Cookie;
use Moltin\Cart\Storage\Session;
use Setting\Bundle\ToolBundle\Event\ReceiveEmailEvent;
use Setting\Bundle\ToolBundle\Event\ReceiveSmsEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{

    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            24  /*limit per page*/
        );
        return $pagination;
    }

   public function productAction($subdomain)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));

        if(!empty($globalOption)){

            $menus = $em->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=>$globalOption,'parent'=>NULL,'menuGroup'=>6),array('sorting'=>'asc'));
            $menusTree = $this->get('setting.menuTreeSettingRepo')->getMenuTree($menus,$subdomain);

            $siteEntity = $em->getRepository('SettingToolBundle:SiteSetting')->findOneBy(array('globalOption'=>$globalOption));
            $mobileTheme = $siteEntity->getMobileTheme();
            if(!empty($siteEntity) && !empty($mobileTheme) ){
                $themeName = $siteEntity->getMobileTheme()->getFolderName();
            }else{
                $themeName ='Default';
            }

            $data = $_REQUEST;
            $inventory = $globalOption->getInventoryConfig();
            $entities = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseVendorItem')->findWithSearch($inventory,$data);
            $pagination = $this->paginate($entities);
            return $this->render('DesktopBundle:'.$themeName.':product.html.twig',
                array(
                    'menu'  => $menusTree,
                    'entity'    => $globalOption,
                    'products'    => $pagination

                )
            );
        }
    }

    public function productDetailsAction($subdomain,PurchaseVendorItem $product)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));

        if(!empty($globalOption)){

            $menus = $em->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=>$globalOption,'parent'=>NULL,'menuGroup'=>6),array('sorting'=>'asc'));
            $menusTree = $this->get('setting.menuTreeSettingRepo')->getMenuTree($menus,$subdomain);

            $siteEntity = $em->getRepository('SettingToolBundle:SiteSetting')->findOneBy(array('globalOption'=>$globalOption));
            $mobileTheme = $siteEntity->getMobileTheme();
            if(!empty($siteEntity) && !empty($mobileTheme) ){
                $themeName = $siteEntity->getMobileTheme()->getFolderName();
            }else{
                $themeName ='Default';
            }

            $data = $_REQUEST;
            $inventory = $globalOption->getInventoryConfig();
            $products = $em->getRepository('InventoryBundle:PurchaseVendorItem')->findWithSearch($inventory,$data);
            $cart = new Cart(new Session(), new Cookie());
            return $this->render('DesktopBundle:'.$themeName.':productDetails.html.twig',
                array(
                    'menu'  => $menusTree,
                    'entity'    => $globalOption,
                    'product'    => $product,
                    'products'    => $products,
                    'cart'    => $cart

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
