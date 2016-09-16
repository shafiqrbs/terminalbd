<?php

namespace Frontend\FrontentBundle\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\InventoryBundle\Entity\GoodsItem;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem;
use Frontend\FrontentBundle\Service\Cart;
use Frontend\FrontentBundle\Service\Item;
use Frontend\FrontentBundle\Service\MobileDetect;
use Frontend\FrontentBundle\Service\ShoppingCart;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;


class WebServiceProductController extends Controller
{

    public function paginate($entities,$limit=15)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            $limit  /*limit per page*/
        );
        $pagination->setTemplate('FrontendBundle:Template/Desktop/Widget:desktopPagination.html.twig');
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
            $entities = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseVendorItem')->findGoodsWithSearch($inventory,$data);
            $pagination = $this->paginate($entities);

            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if( $detect->isMobile() && $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }
            $categoryTree = $this->getDoctrine()->getRepository('InventoryBundle:Product')->getProductCategories($globalOption->getInventoryConfig());
           // $categoryTree = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->build_child();


            $array = array();
            $productCategories = $em->getRepository('ProductProductBundle:Category')->findBy(array('status'=>1),array('name'=>'asc'));
            $category = isset($data['category']) ? $data['category'] :0;
            $categoryTree = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getReturnCategoryTree($category);
            $brands = $this->getDoctrine()->getRepository('InventoryBundle:ItemBrand')->findBy(array('inventoryConfig'=>$globalOption->getInventoryConfig(),'status'=>1),array('name'=>'ASC'));

            return $this->render('FrontendBundle:'.$theme.':product.html.twig',
                array(

                    'globalOption'  => $globalOption,
                    'categoryTree'  => $categoryTree,
                    'brands'  => $brands,
                    'products'    => $pagination,
                )
            );
        }
    }

    public function productDetailsAction($subdomain, $item)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        $entity =  $this->getDoctrine()->getRepository('InventoryBundle:PurchaseVendorItem')->findOneBy(array('inventoryConfig'=>$globalOption->getInventoryConfig(),'slug'=>$item));
        $subItem = $em->getRepository('InventoryBundle:GoodsItem')->findOneBy(array('purchaseVendorItem'=>$entity->getId(),'masterItem'=>1));

        $products ='';
        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            $inventory = $globalOption->getInventoryConfig();

            /*==========Related Product===============================*/

           // if(!empty($entity->getMasterItem())){

                $cat = $entity->getCategory()->getId();
                $data = array('cat' => $cat);
                $entities = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseVendorItem')->findGoodsWithSearch($inventory,$data);
                $products = $this->paginate($entities,$limit=12);
            //}

            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if($detect->isMobile() && $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }
            return $this->render('FrontendBundle:'.$theme.':productDetails.html.twig',
                array(
                    'globalOption'      => $globalOption,
                    'product'           => $entity,
                    'products'          => $products,
                    'subitem'           => $subItem,


                )
            );
        }
    }

    public function productModalAction($subdomain,PurchaseVendorItem $item)
    {

        $em = $this->getDoctrine()->getManager();
        $masterItem = $em->getRepository('InventoryBundle:GoodsItem')->findOneBy(array('purchaseVendorItem'=>$item->getId(),'masterItem'=>1));
        $subItem = isset($_REQUEST['subItem']) ? $_REQUEST['subItem'] : $masterItem->getId() ;
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        $subItem = $em->getRepository('InventoryBundle:GoodsItem')->findOneBy(array('purchaseVendorItem'=>$item,'id'=>$subItem));

        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if($detect->isMobile() && $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }
            return $this->render('FrontendBundle:'.$theme.':productModal.html.twig',
                array(
                    'globalOption'      => $globalOption,
                    'product'           => $item,
                    'subItem'           => $subItem
                )
            );
        }

    }

    public function productSubProductAction($subdomain ,PurchaseVendorItem $product)
    {
        $subItem = $_REQUEST['subItem'];
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        $subItem = $em->getRepository('InventoryBundle:GoodsItem')->findOneBy(array('purchaseVendorItem'=>$product,'id'=>$subItem));
        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if($detect->isMobile() && $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }
            return $this->render('FrontendBundle:'.$theme.':subProduct.html.twig',
                array(
                    'globalOption'    => $globalOption,
                    'product'    => $product,
                    'subItem'    => $subItem
                )
            );
        }
    }


    public function productSubProductCartAction($subdomain ,PurchaseVendorItem $product)
    {
        $subItem = $_REQUEST['subItem'];
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        $subItem = $em->getRepository('InventoryBundle:GoodsItem')->findOneBy(array('purchaseVendorItem'=>$product,'id'=>$subItem));
        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if($detect->isMobile() && $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }
            return $this->render('FrontendBundle:'.$theme.':subProduct.html.twig',
                array(
                    'globalOption'    => $globalOption,
                    'product'    => $product,
                    'subItem'    => $subItem
                )
            );
        }
    }


    public function productAddCartAction(Request $request , $subdomain , PurchaseVendorItem $product, GoodsItem $subitem)
    {

        $cart = new Cart();
        $quantity = $request->request->get('quantity');
        $data = array('id' => $subitem->getId(), 'name'=>$product->getName(),'size'=>$subitem->getSize()->getName(), 'price'=>$subitem->getSalesPrice(),'qty' => $quantity);
        $data1 = array('id'=>2,'name'=>'Lux Soap','size'=>"100 gm",'price'=>'10','qty'=>5);
        $cart->insert($data);
        var_dump($cart->contents());
        return new Response('success');

    }

    public function productCartAction($subdomain)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        $data = $_REQUEST;
        $products ='';
        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            $inventory = $globalOption->getInventoryConfig();

            /*==========Related Product===============================*/

            // if(!empty($entity->getMasterItem())){

            $entities = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseVendorItem')->findGoodsWithSearch($inventory,$data);
            $products = $this->paginate($entities,$limit=12);

            $category = isset($data['category']) ? $data['category'] :0;
            $categoryTree = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getReturnCategoryTree($category);
            $brands = $this->getDoctrine()->getRepository('InventoryBundle:ItemBrand')->findBy(array('inventoryConfig'=>$globalOption->getInventoryConfig(),'status'=>1),array('name'=>'ASC'));


            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if($detect->isMobile() && $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }

            $cart = new Cart();
            //$quantity = $request->request->get('quantity');
            //$data = array('id' => $subitem->getId(), 'name'=>$product->getName(),'size'=>$subitem->getSize()->getName(), 'price'=>$subitem->getSalesPrice(),'qty' => $quantity);
            $data = array('id'=>2,'name'=>'Lux Soap','size'=>"100 gm",'color'=>"Red",'price'=>'10','qty'=>5);
            $cart->insert($data);
            $data1 = array('id'=>3,'name'=>'Dettol','size'=>"200 gm",'color'=>"White",'price'=>'20','qty'=>5);
            $cart->insert($data1);
            return $this->render('FrontendBundle:'.$theme.':cart.html.twig',
                array(
                    'globalOption'      => $globalOption,
                    'categoryTree'      => $categoryTree,
                    'brands'            => $brands,
                    'carts'             => $cart->contents(),
                    'products'          => $products,
                )
            );
        }

    }

    public function productAddWishListAction($subdomain ,PurchaseVendorItem $product)
    {


    }


    public function productCartSaveAction(Request $request , $subdomain)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        $data = $request->request->all();
        $quantity = $request->request->get('quantity');
        $order = $em->getRepository('EcommerceBundle:Order')->insertOrder($globalOption);
       // $em->getRepository('EcommerceBundle:OrderItem')->insertOrderItem($order,$data);



    }


}
