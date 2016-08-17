<?php

namespace Mobile\SettingsBundle\Controller;

use Appstore\Bundle\InventoryBundle\Entity\Item;
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

            $products = $this->getDoctrine()->getRepository('InventoryBundle:Item')->findBy(array('inventoryConfig'=>$globalOption->getInventoryConfig(),'isWeb'=>1),array('updated'=>'DESC'));

            return $this->render('MobileBundle:'.$themeName.':product.html.twig',
                array(
                    'menu'  => $menusTree,
                    'entity'    => $globalOption,
                    'products'    => $products

                )
            );
        }
    }

    public function productDetailsAction($subdomain,$slug)
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

            $product = $this->getDoctrine()->getRepository('InventoryBundle:Item')->findOneBy(array('inventoryConfig'=>$globalOption->getInventoryConfig(),'isWeb'=>1,'slug'=>$slug));
            $products = $this->getDoctrine()->getRepository('InventoryBundle:Item')->findBy(array('inventoryConfig'=>$globalOption->getInventoryConfig(),'isWeb'=>1),array('updated'=>'DESC'));

            $cart = new Cart(new Session(), new Cookie());
            //$cart = var_dump($cart->contents());
            return $this->render('MobileBundle:'.$themeName.':productDetails.html.twig',
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
