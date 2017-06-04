<?php

namespace Frontend\FrontentBundle\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\InventoryBundle\Entity\GoodsItem;
use Appstore\Bundle\InventoryBundle\Entity\ItemBrand;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem;
use Core\UserBundle\Form\CustomerRegisterType;
use Frontend\FrontentBundle\Service\Cart;
use Frontend\FrontentBundle\Service\MobileDetect;
use Product\Bundle\ProductBundle\Entity\Category;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Core\UserBundle\Entity\User;


class WebServiceProductController extends Controller
{

    public function paginate($entities,$limit)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            $limit /*limit per page*/
        );
        $detect = new MobileDetect();
        if( $detect->isMobile() || $detect->isTablet() ) {
            $pagination->setTemplate('FrontendBundle:Template/Desktop/Widget:mobilePagination.html.twig');
        }else{
            $pagination->setTemplate('FrontendBundle:Template/Desktop/Widget:mobilePagination.html.twig');
        }
        return $pagination;
    }

    public function productAction(Request $request , $subdomain)
    {

        $cart = new Cart($request->getSession());
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));

        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            $data = $_REQUEST;
            $ecommerce = $globalOption->getEcommerceConfig();
            $limit = !empty($data['limit'])  ? $data['limit'] : $ecommerce->getPerPage();
            $inventory = $globalOption->getInventoryConfig();
            $entities = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseVendorItem')->findFrontendProductWithSearch($inventory,$data);
            $pagination = $this->paginate($entities, $limit);

            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if( $detect->isMobile() || $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }
            //$category = isset($data['category']) ? $data['category'] :0;

            $inventoryCat = $this->getDoctrine()->getRepository('InventoryBundle:ItemTypeGrouping')->findOneBy(array('inventoryConfig'=>$globalOption->getInventoryConfig()));
            $cats = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getParentId($inventoryCat);
            $categorySidebar = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->productCategorySidebar($cats);
            $brands = $this->getDoctrine()->getRepository('InventoryBundle:ItemBrand')->findBy(array('inventoryConfig'=>$globalOption->getInventoryConfig(),'status'=>1),array('name'=>'ASC'));

            return $this->render('FrontendBundle:'.$theme.':product.html.twig',
                array(
                    'globalOption'  => $globalOption,
                    'cart'                  => $cart,
                    'categorySidebar'  => $categorySidebar,
                    'brands'        => $brands,
                    'products'      => $pagination,
                    'pageName'      => 'Product'

                )
            );
        }
    }

    public function brandAction($subdomain,ItemBrand $brand)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));

        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            $data = $_REQUEST;
            if(empty($data)){
                $data = array('brand' => $brand );
            }
            $ecommerce = $globalOption->getEcommerceConfig();
            $limit = !empty($data['limit'])  ? $data['limit'] : $ecommerce->getPerPage();
            $inventory = $globalOption->getInventoryConfig();
            $entities = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseVendorItem')->findFrontendProductWithSearch($inventory,$data);
            $pagination = $this->paginate($entities, $limit);

            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if( $detect->isMobile() || $detect->isTablet() ) {
                $theme = 'Template/Mobile/Default';
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }

            //$category = isset($data['category']) ? $data['category'] :0;

            $inventoryCat = $this->getDoctrine()->getRepository('InventoryBundle:ItemTypeGrouping')->findOneBy(array('inventoryConfig'=>$globalOption->getInventoryConfig()));
            $cats = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getParentId($inventoryCat);
            $categorySidebar = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->productCategorySidebar($cats);
            $brands = $this->getDoctrine()->getRepository('InventoryBundle:ItemBrand')->findBy(array('inventoryConfig'=>$globalOption->getInventoryConfig(),'status'=>1),array('name'=>'ASC'));

            return $this->render('FrontendBundle:'.$theme.':product.html.twig',
                array(
                    'globalOption'  => $globalOption,
                    'categorySidebar'  => $categorySidebar,
                    'brands'        => $brands,
                    'products'      => $pagination,
                    'pageName'      => 'Brand',
                    'titleName'      => 'Brand: '.$brand->getName(),
                    'data' => $data,
                )
            );
        }
    }

    public function categoryAction($subdomain,Category $category)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));

        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            $data = $_REQUEST;
            if(empty($data)){
                $data = array('category' => $category);
            }
            $ecommerce = $globalOption->getEcommerceConfig();
            $limit = !empty($data['limit'])  ? $data['limit'] : $ecommerce->getPerPage();
            $inventory = $globalOption->getInventoryConfig();
            $entities = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseVendorItem')->findFrontendProductWithSearch($inventory,$data);
            $pagination = $this->paginate($entities, $limit);

            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if( $detect->isMobile() || $detect->isTablet() ) {
                $theme = 'Template/Mobile/Default';
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }

            //$category = isset($data['category']) ? $data['category'] :0;

            $inventoryCat = $this->getDoctrine()->getRepository('InventoryBundle:ItemTypeGrouping')->findOneBy(array('inventoryConfig'=>$globalOption->getInventoryConfig()));
            $cats = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getParentId($inventoryCat);
            $categorySidebar = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->productCategorySidebar($cats);
            $brands = $this->getDoctrine()->getRepository('InventoryBundle:ItemBrand')->findBy(array('inventoryConfig'=>$globalOption->getInventoryConfig(),'status'=>1),array('name'=>'ASC'));

            return $this->render('FrontendBundle:'.$theme.':product.html.twig',
                array(
                    'globalOption'          => $globalOption,
                    'categorySidebar'       => $categorySidebar,
                    'brands'                => $brands,
                    'products'              => $pagination,
                    'pageName'              => 'Product',
                    'data'                  => $data['limit']=4,
                    'titleName'             => 'Category: '.$category->getName(),
                )
            );
        }
    }

    public function promotionAction($subdomain)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));

        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            $data = $_REQUEST;
            $inventory = $globalOption->getInventoryConfig();
            $entities = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseVendorItem')->findGoodsWithSearch($inventory,$data);
            $ecommerce = $globalOption->getEcommerceConfig();
            $limit = !empty($data['limit'])  ? $data['limit'] : $ecommerce->getPerPage();
            $pagination = $this->paginate($entities,$limit);

            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if( $detect->isMobile() || $detect->isTablet() ) {
                $theme = 'Template/Mobile/Default';
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

    public function tagAction($subdomain)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));

        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            $data = $_REQUEST;
            $inventory = $globalOption->getInventoryConfig();
            $entities = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseVendorItem')->findGoodsWithSearch($inventory,$data);
            $ecommerce = $globalOption->getEcommerceConfig();
            $limit = !empty($data['limit'])  ? $data['limit'] : $ecommerce->getPerPage();
            $pagination = $this->paginate($entities,$limit);

            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if( $detect->isMobile() || $detect->isTablet() ) {
                $theme = 'Template/Mobile/Default';
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

    public function discountAction($subdomain)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));

        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            $data = $_REQUEST;
            $inventory = $globalOption->getInventoryConfig();
            $entities = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseVendorItem')->findGoodsWithSearch($inventory,$data);
            $ecommerce = $globalOption->getEcommerceConfig();
            $limit = !empty($data['limit'])  ? $data['limit'] : $ecommerce->getPerPage();
            $pagination = $this->paginate($entities,$limit);

            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if( $detect->isMobile() || $detect->isTablet() ) {
                $theme = 'Template/Mobile/Default';
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

    public function productDetailsAction(Request $request , $subdomain, $item)
    {
        $cart = new Cart($request->getSession());
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        $entity =  $this->getDoctrine()->getRepository('InventoryBundle:PurchaseVendorItem')->findOneBy(array('inventoryConfig'=>$globalOption->getInventoryConfig(),'slug'=>$item));
        $subItem = $em->getRepository('InventoryBundle:GoodsItem')->findOneBy(array('purchaseVendorItem'=>$entity->getId(),'masterItem'=>1));

        $products ='';
        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            $inventory = $globalOption->getInventoryConfig();

            /*==========Related Product===============================*/

            if(!empty($entity->getMasterItem()) && !empty ($entity->getMasterItem()->getCategory())){

                $cat = $entity->getMasterItem()->getCategory()->getId();
                $data = array('cat' => $cat);
                $entities = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseVendorItem')->findGoodsWithSearch($inventory,$data);
                $products = $this->paginate($entities,$limit=12);
            }

            $inventoryCat = $this->getDoctrine()->getRepository('InventoryBundle:ItemTypeGrouping')->findOneBy(array('inventoryConfig'=>$globalOption->getInventoryConfig()));
            $cats = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getParentId($inventoryCat);
            $categorySidebar = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->productCategorySidebar($cats);
            $brands = $this->getDoctrine()->getRepository('InventoryBundle:ItemBrand')->findBy(array('inventoryConfig'=>$globalOption->getInventoryConfig(),'status'=>1),array('name'=>'ASC'));

            $next = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseVendorItem')->frontendProductNext($entity);
            $previous = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseVendorItem')->frontendProductPrev($entity);

            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if($detect->isMobile() || $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }

            return $this->render('FrontendBundle:'.$theme.':productDetails.html.twig',

                array(

                    'globalOption'      => $globalOption,
                    'cart'              => $cart,
                    'categorySidebar'   => $categorySidebar,
                    'brands'            => $brands,
                    'product'           => $entity,
                    'products'          => $products,
                    'subitem'           => $subItem,
                    'next'              => $next,
                    'previous'          => $previous,
                    'pageName'          => 'ProductDetails',
                )
            );
        }
    }

    public function productModalAction($subdomain,PurchaseVendorItem $item)
    {

        $em = $this->getDoctrine()->getManager();
        $masterItem = $em->getRepository('InventoryBundle:GoodsItem')->findOneBy(array('purchaseVendorItem'=>$item->getId(),'masterItem'=>1));
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        if(empty($masterItem)){
        $subItem ='';
        }else{
        $subItem = isset($_REQUEST['subItem']) ? $_REQUEST['subItem'] : $masterItem->getId() ;
        $subItem = $em->getRepository('InventoryBundle:GoodsItem')->findOneBy(array('purchaseVendorItem'=>$item,'id'=>$subItem));
        }
        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if($detect->isMobile() || $detect->isTablet() ) {
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
            if($detect->isMobile() || $detect->isTablet() ) {
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
            if($detect->isMobile() || $detect->isTablet() ) {
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

        $cart = new Cart($request->getSession());
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));

        $quantity = $request->request->get('quantity');
        $color = $request->request->get('color');
        $productImg = $request->request->get('productImg');

        $color = !empty($color) ? $color : 0;
        if($color > 0){
            $colorName = $this->getDoctrine()->getRepository('InventoryBundle:ItemColor')->find($color)->getName();
        }else{
            $colorName ='';
        }
        /** @var GlobalOption $globalOption */

        $showMaster = $globalOption->getEcommerceConfig()->getShowMasterName();

        echo $masterItem = !empty($product->getMasterItem()) and $showMaster == 1 ? $product->getMasterItem()->getName().'-':'';

        $data = array(

            'id' => $subitem->getId(),
            'name'=> $masterItem.' '.$product->getWebName(),
            'brand'=> !empty($product->getBrand()) ? $product->getBrand()->getName():'',
            'category'=> !empty($product->getMasterItem()->getCategory()) ? $product->getMasterItem()->getCategory()->getName():'',
            'size'=>!empty($subitem->getSize()) ? $subitem->getSize()->getName():0 ,
            'color'=> $colorName ,
            'colorId'=> $color,
            'price'=> $subitem->getSalesPrice(),
            'quantity' => $quantity,
            'productImg' => $productImg
        );
        $cart->insert($data);
        $cartTotal = $cart->total();
        $totalItems = $cart->total_items();
        $cartResult = $cartTotal.'('.$totalItems.')';
        $salesItems = $this->getCartItem($globalOption,$cart->contents());
        return new Response(json_encode(array('cartResult' => $cartResult,'cartTotal' => $cartTotal,'totalItem' => $totalItems, 'salesItem' => $salesItems)));


    }

    public function getCartItem(GlobalOption $globalOption , $salesItems){


        $currency = $globalOption->getEcommerceConfig()->getCurrency();

       $items = '';

       foreach ($salesItems as $product ) {

            $items .= '<li id="item-remove-'.$product['rowid'].'" ><div class="item">';
            $items .= '<div class="col-md-12 cart-product-title">'.$product['name'].'</div>';
            $items .= '<div class="col-md-6">';
            $items .= '<img height="100" width="160" src="'.$product['productImg'] . '">';
            $items .= '</div>';
            $items .= '<div class="col-md-6">';
            $items .= '<div class="input-group">';
            $items .= '<span class="input-group-addon">'.$currency .' '. $product['price'].'</span>';
            $items .= '<input type="text" class="form-control" width="80" value="' . $product['quantity'] . '">';
            $items .= '</div>';
            $items .= '<div class="btn-group text-right col-md-12" role="group" >';
            $items .= '<button id="'.$product['rowid'].'"  data-url="/cart/product-remove/'.$product['rowid'] .'"
                                                            class="btn btn-danger pull-right hunger-remove-cart"><span class="glyphicon glyphicon-trash"></span>
                                                    </button>';
            $items .= '<button id="'.$product['rowid'].'"  data-url="/cart/product-update/'.$product['rowid'] .'" data-id="'. $product['price'] .'" data-value="'.$product['quantity'].'" 
                                                            class="btn btn-success pull-right hunger-update-cart"><span class="glyphicon glyphicon-pencil"></span>
                                                    </button>';
            $items .= '</div>';
            $items .= '</div>';
            $items .= '</div></li>';
        }
        return $items;

    }

    public function productUpdateCartAction(Request $request , $cartid)
    {
        $cart = new Cart($request->getSession());
        $quantity = $_REQUEST['quantity'];
        $price =$_REQUEST['price'];
        $data = array(

            'rowid' => $cartid,
            'price'=>$price,
            'quantity' => $quantity,
        );
        $cart->update($data);
        $cartTotal = $cart->total();
        $totalItems = $cart->total_items();
        $cartResult = $cartTotal.'('.$totalItems.')';
        return new Response($cartResult);

    }

    public function productRemoveCartAction(Request $request , $cartid)
    {
        $cart = new Cart($request->getSession());
        $cart->remove($cartid);
        $cartTotal = $cart->total();
        $totalItems = $cart->total_items();
        $cartResult = $cartTotal.'('.$totalItems.')';
        $salesItems = $this->getCartItem($cart);
        return new Response(json_encode(array('cartResult' => $cartResult,'cartTotal' => $cartTotal,'totalItem' => $totalItems, 'salesItem' => $salesItems)));


    }

    public function productCartAction($subdomain, Request $request)
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


            $inventoryCat = $this->getDoctrine()->getRepository('InventoryBundle:ItemTypeGrouping')->findOneBy(array('inventoryConfig'=>$globalOption->getInventoryConfig()));
            $cats = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getParentId($inventoryCat);
            $categorySidebar = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->productCategorySidebar($cats);
            $brands = $this->getDoctrine()->getRepository('InventoryBundle:ItemBrand')->findBy(array('inventoryConfig'=>$globalOption->getInventoryConfig(),'status'=>1),array('name'=>'ASC'));


            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if($detect->isMobile() && $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Mobile/'.$themeName;
            }

            $cart = new Cart($request->getSession());

            //$quantity = $request->request->get('quantity');
            //$data = array('id' => $subitem->getId(), 'name'=>$product->getName(),'size'=>$subitem->getSize()->getName(), 'price'=>$subitem->getSalesPrice(),'qty' => $quantity);



            return $this->render('FrontendBundle:'.$theme.':cart.html.twig',
                array(
                    'globalOption'      => $globalOption,
                    'brands'            => $brands,
                    'cart'             => $cart,
                    'pageName'          => 'Cart',
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
