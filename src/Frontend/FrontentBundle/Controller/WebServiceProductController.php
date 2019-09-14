<?php

namespace Frontend\FrontentBundle\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\Item;
use Appstore\Bundle\EcommerceBundle\Entity\ItemBrand;
use Appstore\Bundle\EcommerceBundle\Entity\ItemSub;
use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\EcommerceBundle\Entity\Promotion;
use Core\UserBundle\Form\CustomerRegisterType;
use Frontend\FrontentBundle\Service\Cart;
use Frontend\FrontentBundle\Service\MobileDetect;
use Product\Bundle\ProductBundle\Entity\Category;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Core\UserBundle\Entity\User;


class WebServiceProductController extends Controller
{

    public function paginate($entities, $limit , $template = '')
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            $limit /*limit per page*/
        );
        $detect = new MobileDetect();
        if( $detect->isMobile() || $detect->isTablet() ) {
            if($template =='nextPrev'){
                $pagination->setTemplate('FrontendBundle:Template/Mobile/Pagination:nextPrev.html.twig');
            }elseif($template =='nextPrevDropDown') {
                $pagination->setTemplate('FrontendBundle:Template/Mobile/Pagination:nextPrevDropDown.html.twig');
            }else{
                $pagination->setTemplate('FrontendBundle:Template/Mobile/Pagination:bootstrap.html.twig');
            }
        }else{
            if($template =='nextPrev'){
                $pagination->setTemplate('FrontendBundle:Template/Desktop/Pagination:nextPrev.html.twig');
            }elseif($template =='nextPrevDropDown') {
                $pagination->setTemplate('FrontendBundle:Template/Desktop/Pagination:nextPrevDropDown.html.twig');
            }else{
                $pagination->setTemplate('FrontendBundle:Template/Desktop/Pagination:bootstrap.html.twig');
            }
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
            $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=> $globalOption ,'slug' => 'product'));
            $post = array();
            $post = empty($_REQUEST['item']) ? '' : $_REQUEST['item'];
            $data = $_REQUEST;
            $data['category']= isset($post['category']) ? $post['category']:'';
            $data['brand']= isset($post['brand']) ? $post['brand']:'';
            $data['name']= isset($post['webName']) ? $post['webName']:'';

            $config = $globalOption->getEcommerceConfig();
            $limit = !empty($data['limit'])  ? $data['limit'] : $config->getPerPage();
            $entities = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->findFrontendProductWithSearch($config->getId(),$data);
            $pagination = $this->paginate($entities, $limit,$globalOption->getTemplateCustomize()->getPagination());

            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if( $detect->isMobile() || $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }
            $searchForm = !empty($_REQUEST) ? $_REQUEST :array();
            return $this->render('FrontendBundle:'.$theme.':product.html.twig',
                array(
                    'globalOption'      => $globalOption,
                    'cart'              => $cart,
                    'products'          => $pagination,
                    'menu'              => $menu,
                    'pageName'          => 'Product',
                    'searchForm'        => $searchForm,
                )
            );
        }
    }

    public function productFilter(Request $request , $subdomain)
    {
        $cart = new Cart($request->getSession());
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));

        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=> $globalOption ,'slug' => 'product'));

            $data = $_REQUEST;
            $config = $globalOption->getEcommerceConfig();
            $limit = !empty($data['limit'])  ? $data['limit'] : $config->getPerPage();
            $entities = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->findFrontendProductWithSearch($config->getId(),$data);
            $pagination = $this->paginate($entities, $limit,$globalOption->getTemplateCustomize()->getPagination());

            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if( $detect->isMobile() || $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }
            $searchForm = !empty($_REQUEST) ? $_REQUEST :array();
            return $this->render('FrontendBundle:'.$theme.':product.html.twig',
                array(
                    'globalOption'      => $globalOption,
                    'cart'              => $cart,
                    'products'          => $pagination,
                    'menu'              => $menu,
                    'searchForm'        => $searchForm,
                    'pageName'          => 'Product',
                )
            );
        }
    }

    public function productSearchAction(Request $request , $subdomain)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        if(!empty($globalOption)){
            if($globalOption->getDomainType() == 'ecommerce'){
                return $this->productFilter($request , $subdomain);
            }elseif ($globalOption->getDomainType() == 'medicine'){
                return $this->medicineProductSearch($request , $subdomain);
            }
        }else{
            return $this->redirect($this->generateUrl('homepage'));
        }
    }

    public function stockSearchAction($subdomain)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        $item = trim($_REQUEST['q']);
        $search_arr = array();
        if ($item) {
            $config = $globalOption->getMedicineConfig();
            $items = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->searchWebStock($item,$config);
            foreach ($items as $item):
                $id = $item['id'];
                $name = $item['text'];
                $search_arr[] = array("id" => $id, "name" => $name);
            endforeach;
        }
        echo json_encode($search_arr);
        exit;
    }

    public function stockItemDetailsAction($subdomain)
    {
        $id = $_REQUEST['stockId'];
        $entity = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->find($id);
        return new Response(json_encode(array('price' => $entity->getSalesPrice() , 'unit' => $entity->getUnit()->getName())));

    }

    public function medicineProductSearch($request , $subdomain)
    {

        $cart = new Cart($request->getSession());
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));

        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=> $globalOption ,'slug' => 'product'));

            $data = $_REQUEST;
            $config = $globalOption->getEcommerceConfig();
            $entities = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->findFrontendProductWithSearch($config->getId(),$data);
            $medicineConfig = $globalOption->getMedicineConfig()->getId();
            $vendorEntities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->findWithSearch($medicineConfig,$data);
           $globalEntities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->getMedicineBrandSearch($data);

            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if( $detect->isMobile() || $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }
            $item = !empty($_REQUEST) ? $_REQUEST :array();
            $searchForm = $item['item'];
            return $this->render('FrontendBundle:'.$theme.':productSearch.html.twig',
                array(
                    'globalOption'      => $globalOption,
                    'cart'              => $cart,
                    'products'          => $entities,
                    'vendorEntities'    => $vendorEntities,
                    'globalEntities'    => $globalEntities,
                    'menu'              => $menu,
                    'searchForm'        => $searchForm,
                    'pageName'          => 'Product',
                )
            );
        }
    }

    public function brandAction(Request $request , $subdomain, ItemBrand $brand)
    {

        $cart = new Cart($request->getSession());
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain' => $subdomain));

        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption' => $globalOption ,'slug' => 'brand'));

            $data = $_REQUEST;
            if(empty($data)){
                $data = array('brand' => $brand );
            }
            $config = $globalOption->getEcommerceConfig();
            $limit = !empty($data['limit'])  ? $data['limit'] : $config->getPerPage();
            $entities = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->findFrontendProductWithSearch($config->getId(),$data);
            $pagination = $this->paginate($entities, $limit , $globalOption->getTemplateCustomize()->getPagination());

            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if( $detect->isMobile() || $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }
            $searchForm = !empty($_REQUEST) ? $_REQUEST :array();

            return $this->render('FrontendBundle:'.$theme.':product.html.twig',
                array(
                    'titleName'         => 'Brand: '.$brand->getName(),
                    'globalOption'      => $globalOption,
                    'cart'              => $cart,
                    'products'          => $pagination,
                    'menu'              => $menu,
                    'pageName'          => 'Product',
                    'searchForm'        => $searchForm,
                )
            );
        }
    }

    public function categoryAction(Request $request , $subdomain, Category $category)
    {

        $cart = new Cart($request->getSession());
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));

        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=> $globalOption ,'slug' => 'category'));

            $data = $_REQUEST;
            if(empty($data)){
                $data = array('categoryId' => $category->getId());
            }

            $config = $globalOption->getEcommerceConfig();
            $limit = !empty($data['limit'])  ? $data['limit'] : $config->getPerPage();
            $entities = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->findFrontendProductWithSearch($config->getId(),$data);
            $pagination = $this->paginate($entities, $limit,$globalOption->getTemplateCustomize()->getPagination());

            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if( $detect->isMobile() || $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }

            return $this->render('FrontendBundle:'.$theme.':product.html.twig',
                array(
                    'globalOption'          => $globalOption,
                    'cart'                  => $cart,
                    'products'              => $pagination,
                    'menu'                  => $menu,
                    'pageName'              => 'Product',
                    'data'                  => $data['limit']= 20,
                    'searchForm'            => $data,
                    'titleName'             => 'Category: '.$category->getName(),
                )
            );
        }
    }

    public function promotionAction(Request $request , $subdomain, Promotion $promotion)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));

        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=> $globalOption ,'slug' => 'promotion'));

            $data = $_REQUEST;
            $config = $globalOption->getEcommerceConfig();
            $limit = !empty($data['limit'])  ? $data['limit'] : $config->getPerPage();
            $entities = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->findFrontendProductWithSearch($config->getId(),$data);
            $pagination = $this->paginate($entities,$limit,$globalOption->getTemplateCustomize()->getPagination());

            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if( $detect->isMobile() || $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }

            return $this->render('FrontendBundle:'.$theme.':product.html.twig',
                array(

                    'globalOption'  => $globalOption,
                    'menu'  => $menu,
                    'searchForm'        => $data,
                    'products'    => $pagination,
                )
            );
        }
    }

    public function tagAction(Request $request , $subdomain, Promotion $tag)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));

        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=> $globalOption ,'slug' => 'tag'));


            $data = $_REQUEST;
            $config = $globalOption->getEcommerceConfig();
            $limit = !empty($data['limit'])  ? $data['limit'] : $config->getPerPage();
            $entities = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->findFrontendProductWithSearch($config->getId(),$data);
            $pagination = $this->paginate($entities,$limit,$globalOption->getTemplateCustomize()->getPagination());

            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if( $detect->isMobile() || $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }
            return $this->render('FrontendBundle:'.$theme.':product.html.twig',
                array(

                    'globalOption'  => $globalOption,
                    'menu'  => $menu,
                    'searchForm'        => $data,
                    'products'    => $pagination,
                )
            );
        }
    }

    public function discountAction(Request $request , $subdomain)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));

        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=> $globalOption ,'slug' => 'category'));


            $data = $_REQUEST;
            $config = $globalOption->getEcommerceConfig();
            $limit = !empty($data['limit'])  ? $data['limit'] : $config->getPerPage();
            $entities = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->findFrontendProductWithSearch($config->getId(),$data);
            $pagination = $this->paginate($entities,$limit,$globalOption->getTemplateCustomize()->getPagination());

            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if( $detect->isMobile() || $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }
            $category = isset($data['category']) ? $data['category'] :0;
            $categoryTree = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getReturnCategoryTree($category);
            $searchForm = !empty($_REQUEST) ? $_REQUEST :array();
            return $this->render('FrontendBundle:'.$theme.':product.html.twig',
                array(

                    'globalOption'  => $globalOption,
                    'categoryTree'  => $categoryTree,
                    'menu'  => $menu,
                    'searchForm'        => $searchForm,
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
        $entity =  $this->getDoctrine()->getRepository('EcommerceBundle:Item')->findOneBy(array('ecommerceConfig'=>$globalOption->getEcommerceConfig(),'slug'=>$item));
        $subItem = $em->getRepository('EcommerceBundle:ItemSub')->findOneBy(array('item'=>$entity->getId(),'masterItem'=>1));

        $products ='';
        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=> $globalOption ,'slug' => 'product-details'));



            /*==========Related Product===============================*/

            if(!empty ($entity->getCategory())){

                $cat = $entity->getCategory()->getId();
                $data = array('category' => $cat);
                $config = $globalOption->getEcommerceConfig()->getId();
                $entities = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->findFrontendProductWithSearch($config,$data);
                $products = $this->paginate($entities, $limit = 12 , $globalOption->getTemplateCustomize()->getPagination());
            }


            $next = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->frontendProductNext($entity);
            $previous = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->frontendProductPrev($entity);

            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if($detect->isMobile() || $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }

            $searchForm = !empty($_REQUEST) ? $_REQUEST :array();

            return $this->render('FrontendBundle:'.$theme.':productDetails.html.twig',

                array(
                    'globalOption'      => $globalOption,
                    'cart'              => $cart,
                    'product'           => $entity,
                    'products'          => $products,
                    'subItem'           => $subItem,
                    'next'              => $next,
                    'previous'          => $previous,
                    'menu'              => $menu,
                    'searchForm'        => $searchForm,
                    'pageName'          => 'ProductDetails',
                )
            );
        }
    }

    public function productModalAction($subdomain,Item $item)
    {

        $em = $this->getDoctrine()->getManager();
        $masterItem = $em->getRepository('EcommerceBundle:ItemSub')->findOneBy(array('item'=>$item->getId(),'masterItem'=>1));
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        if(empty($masterItem)){
        $subItem ='';
        }else{
        $subItem = isset($_REQUEST['subItem']) ? $_REQUEST['subItem'] : $masterItem->getId() ;
        $subItem = $em->getRepository('EcommerceBundle:ItemSub')->findOneBy(array('item' => $item,'id' => $subItem));
        }
        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            /* Device Detection code desktop or mobile */
            $next = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->frontendProductNext($item);
            $previous = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->frontendProductPrev($item);

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
                    'next'              => $next,
                    'previous'          => $previous,
                    'subItem'           => $subItem
                )
            );
        }

    }

    public function productSubProductAction($subdomain)
    {
        $subId = $_REQUEST['subItem'];
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=> $subdomain));
        /* @var ItemSub $subItem */
        $subItem = $em->getRepository('EcommerceBundle:ItemSub')->find($subId);
        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            /* Device Detection code desktop or mobile */
            $next = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->frontendProductNext($subItem->getItem());
            $previous = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->frontendProductPrev($subItem->getItem());

            $detect = new MobileDetect();
            if($detect->isMobile() || $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }
            $html =  $this->renderView('FrontendBundle:'.$theme.':subProduct.html.twig',
                array(
                    'globalOption'      => $globalOption,
                    'product'           => $subItem->getItem(),
                    'next'              => $next,
                    'previous'          => $previous,
                    'subItem'           => $subItem
                )
            );

            echo $array = (json_encode(array('subItem' => $html ,'subItemQuantity' => $subItem->getQuantity())));
            exit;
        }
    }

    public function inlineSubProductAction($subdomain ,Item $product)
    {

        $subId = $_REQUEST['subItem'];
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        /* @var ItemSub $subItem */
        $subItem = $em->getRepository('EcommerceBundle:ItemSub')->findOneBy(array('item'=> $product,'id'=> $subId));
        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if($detect->isMobile() || $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }
            $html =  $this->renderView('FrontendBundle:'.$theme.':inlineSubProduct.html.twig',
                array(
                    'globalOption'    => $globalOption,
                    'product'    => $product,
                    'subItem'    => $subItem
                )
            );
            if($subItem->getDiscountPrice()){
                $price = '<strike>'.$subItem->getSalesPrice().'</strike> <strong class="list-price" >'.$subItem->getDiscountPrice().'</strong>';
            }else{
                $price = '<strong class="list-price">'.$subItem->getSalesPrice().'</strong>';
            }
            echo $array = (json_encode(array('subItem' => $html ,'salesPrice' => $price )));
            exit;
        }
    }

    public function productSubProductCartAction($subdomain ,Item $product)
    {
        $subItem = $_REQUEST['subItem'];
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        $subItem = $em->getRepository('EcommerceBundle:ItemSub')->findOneBy(array('item'=>$product,'id'=>$subItem));
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

    public function productAddCartAction(Request $request , $subdomain , Item $product, ItemSub $subitem)
    {

        $cart = new Cart($request->getSession());
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain' => $subdomain));

        $quantity = $request->request->get('quantity');
        $color = $request->request->get('color');
        $productImg = $request->request->get('productImg');

        $color = !empty($color) ? $color : 0;
        if ($color > 0) {
            $colorName = $this->getDoctrine()->getRepository('SettingToolBundle:ProductColor')->find($color)->getName();
        } else {
            $colorName = '';
        }
        /** @var GlobalOption $globalOption */

        $showMaster = $globalOption->getEcommerceConfig()->getShowMasterName();
        $salesPrice = $subitem->getDiscountPrice() == null ?  $subitem->getSalesPrice() : $subitem->getDiscountPrice();
       // $masterItem = (!empty($product->getMasterItem()) and $showMaster == 1) ? $product->getMasterItem()->getName() . ' ' : '';
        $sizeUnit = !empty($subitem->getProductUnit()) ? $subitem->getProductUnit()->getName() : '';
        $productUnit = (!empty($product->getProductUnit())) ? $product->getProductUnit()->getName() : '';

        if (!empty($subitem) and $subitem->getQuantity() >= $quantity) {
            $data = array(
                'id' => $subitem->getId(),
                'name' => $product->getWebName(),
                'brand' => !empty($product->getBrand()) ? $product->getBrand()->getName() : '',
                'category' => !empty($product->getCategory()) ? $product->getCategory()->getName() : '',
                'size' => !empty($subitem->getSize()) ? $subitem->getSize()->getName() : 0,
                'sizeUnit' => $sizeUnit,
                'productUnit' => $productUnit,
                'color' => $colorName,
                'colorId' => $color,
                'price' => $salesPrice,
                'quantity' => $quantity,
                'maxQuantity' => $subitem->getQuantity(),
                'productImg' => $productImg
            );

            $cart->insert($data);
            $cartTotal = (string)$cart->total();
            $totalItems = (string)$cart->total_items();
            $cartResult = $cartTotal.'('.$totalItems.')';
            $array =(json_encode(array('process'=>'success','cartResult' => $cartResult,'cartTotal' => $cartTotal,'totalItem' => $totalItems)));
        }else{
            $array =(json_encode(array('process'=>'invalid')));
        }
        echo $array;
        exit;

    }


    public function productAddSingleCartAction(Request $request , $subdomain , Item $product)
    {

        $cart = new Cart($request->getSession());
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain' => $subdomain));

        $data = $_REQUEST;
        $color = isset($data['color']) ? $data['color'] :'';
        $size =  isset($data['size']) ? $data['size'] :'';
        $productImg = isset($data['productImg']) ? $data['productImg'] :'';

        /* @var ItemSub $subitem */

        if(empty($size)){
            $subitem = $em->getRepository('EcommerceBundle:ItemSub')->findOneBy(array('item'=>$product,'masterItem' => 1));
        }else{
            $subitem = $em->getRepository('EcommerceBundle:ItemSub')->findOneBy(array('item'=>$product,'id' => $size));
        }
        $quantity = 1;
        $color = !empty($color) ? $color : 0;
        if ($color > 0) {
            $colorName = $this->getDoctrine()->getRepository('InventoryBundle:ItemColor')->find($color)->getName();
        } else {
            $colorName = '';
        }

        /** @var GlobalOption $globalOption */
        $showMaster = $globalOption->getEcommerceConfig()->getShowMasterName();
        $salesPrice = $subitem->getDiscountPrice() == null ?  $subitem->getSalesPrice() : $subitem->getDiscountPrice();
        $sizeUnit = !empty($subitem->getProductUnit()) ? $subitem->getProductUnit()->getName() : '';
        $productUnit = (!empty($product->getProductUnit())) ? $product->getProductUnit()->getName() : '';

        if (!empty($subitem) and $subitem->getQuantity() >= $quantity) {
            $data = array(
                'id' => $subitem->getId(),
                'name' => $product->getWebName(),
                'brand' => !empty($product->getBrand()) ? $product->getBrand()->getName() : '',
                'category' => !empty($product->getCategory()) ? $product->getCategory()->getName() : '',
                'size' => !empty($subitem->getSize()) ? $subitem->getSize()->getName() : 0,
                'sizeUnit' => $sizeUnit,
                'productUnit' => $productUnit,
                'color' => $colorName,
                'colorId' => $color,
                'price' => $salesPrice,
                'quantity' => $quantity,
                'maxQuantity' => $subitem->getQuantity(),
                'productImg' => $productImg
            );
            $cart->insert($data);
            $cartTotal = (string)$cart->total();
            $totalItems = (string)$cart->total_items();
            $cartResult = $cartTotal.'('.$totalItems.')';
            $array =(json_encode(array('process'=>'success','cartResult' => $cartResult,'cartTotal' => $cartTotal,'totalItem' => $totalItems)));
        }else{
            $array =(json_encode(array('process'=>'invalid')));
        }
        echo $array;
        exit;

    }


    public function productAddMedicineCartAction(Request $request , $subdomain , Item $product)
    {

        $cart = new Cart($request->getSession());
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain' => $subdomain));

        $data = $_REQUEST;
        $quantity =  isset($data['quantity']) ? $data['quantity'] :'';
        $productImg = isset($data['productImg']) ? $data['productImg'] :'';
        $salesPrice = $product->getDiscountPrice() == null ?  $product->getSalesPrice() : $product->getDiscountPrice();
        $productUnit = (!empty($product->getProductUnit())) ? $product->getProductUnit()->getName() : '';

        /** @var $globalOption GlobalOption */

            $data = array(
                'id' => $product->getId(),
                'name' => $product->getWebName(),
                'brand' => !empty($product->getBrand()) ? $product->getBrand()->getName() : '',
                'category' => !empty($product->getCategory()) ? $product->getCategory()->getName() : '',
                'price' => $salesPrice,
                'quantity' => $quantity,
                'productUnit' => $productUnit,
                'productImg' => $productImg
            );
            $cart->insert($data);
            $cartTotal = (string)$cart->total();
            $totalItems = (string)$cart->total_items();
            $cartResult = $cartTotal.'('.$totalItems.')';
            $array =(json_encode(array('process'=>'success','cartResult' => $cartResult,'cartTotal' => $cartTotal,'totalItem' => $totalItems)));
        echo $array;
        exit;

    }

    public function prescriptionPreviewAction(Request $request , $subdomain)
    {

        $cart = new Cart($request->getSession());
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain' => $subdomain));
        $detect = new MobileDetect();
        if($detect->isMobile() || $detect->isTablet() ) {
            $theme = 'Template/Mobile/Medicine/';
        }else{
            $theme = 'Template/Desktop/Medicine/';
        }
        $html = $this->renderView(
            'FrontendBundle:'.$theme.':prescription.html.twig', array(
                'cart' => $cart,
                'globalOption' => $globalOption
            )
        );
        return new Response($html);

    }

    public function stockProductToCartAction(Request $request , $subdomain)
    {

        $cart = new Cart($request->getSession());
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain' => $subdomain));

        $data = $request->request->all();

        if(!empty($data)) {

            $product = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->find($data['stockId']);
            $quantity = isset($data['itemQuantity']) ? $data['itemQuantity'] : '';
            $productUnit = (!empty($product->getUnit())) ? $product->getUnit()->getName() : '';

            /** @var GlobalOption $globalOption */

            $data = array(
                'id' => $product->getId(),
                'name' => $product->getName(),
                'brand' => $product->getBrandName(),
                'category' => '',
                'price' => $product->getSalesPrice(),
                'quantity' => $quantity,
                'productUnit' => $productUnit,
                'productImg' => ''
            );
            $cart->insert($data);
        }

        $detect = new MobileDetect();
        if($detect->isMobile() || $detect->isTablet() ) {
            $theme = 'Template/Mobile/Medicine/';
        }else{
            $theme = 'Template/Desktop/Medicine/';
        }
        $html = $this->renderView(
            'FrontendBundle:'.$theme.':stockCart.html.twig', array(
                'cart' => $cart,
                'globalOption' => $globalOption
            )
        );
        return new Response($html);

    }

    public function productCartDetailsAction(Request $request, $subdomain){


        $cart = new Cart($request->getSession());
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        /* Device Detection code desktop or mobile */
        $theme = "";
        if(!empty($globalOption)) {
            if ($globalOption->getDomainType() == 'ecommerce') {
                $theme = 'Template/Mobile/EcommerceWidget:ajaxCart.html.twig';
            } elseif ($globalOption->getDomainType() == 'medicine') {
                $theme = 'Template/Mobile/EcommerceWidget:ajaxMedicineCart.html.twig';
            }
        }
        return $this->render("FrontendBundle:{$theme}",
            array(
                'cart' => $cart,
                'searchForm'        => $_REQUEST,
                'globalOption' => $globalOption
            )
        );
    }


    public function productUpdateCartAction(Request $request , $cartid)
    {
        $em = $this->getDoctrine()->getManager();
        $cart = new Cart($request->getSession());
        $quantity = (int)$_REQUEST['quantity'];
        $productId = (int)$_REQUEST['productId'];
        $price = (float)$_REQUEST['price'];
        $item = $this->getDoctrine()->getRepository('EcommerceBundle:ItemSub')->find($productId);
        if (!empty($item) and  $item->getQuantity() >= $quantity){
            $data = array(
                'rowid' => $cartid,
                'price' => $price,
                'quantity' => $quantity,
            );
            $cart->update($data);
            $cartTotal = (string)$cart->total();
            $totalItems = (string)$cart->total_items();
            $cartResult = $cartTotal.'('.$totalItems.')';
            $array =(json_encode(array('process'=>'success','cartResult' => $cartResult,'cartTotal' => $cartTotal,'totalItem' => $totalItems)));

        }else{
            $array =(json_encode(array('process'=>'invalid')));
        }
        echo $array;
        exit;
    }

    public function productMedicineUpdateCartAction(Request $request , $cartid)
    {
        $em = $this->getDoctrine()->getManager();
        $cart = new Cart($request->getSession());
        $quantity = (int)$_REQUEST['quantity'];
        $productId = (int)$_REQUEST['productId'];
        $price = (float)$_REQUEST['price'];
        $data = array(
            'rowid' => $cartid,
            'price' => $price,
            'quantity' => $quantity,
        );
        $cart->update($data);
        $cartTotal = (string)round($cart->total());
        $totalItems = (string)$cart->total_items();
        $cartResult = $cartTotal.'('.$totalItems.')';
        $array =(json_encode(array('process'=>'success','cartResult' => $cartResult,'cartTotal' => $cartTotal,'totalItem' => $totalItems)));
        echo $array;
        exit;
    }

    public function getCartItem(GlobalOption $globalOption , Cart $cart){

        $currency = $globalOption->getEcommerceConfig()->getCurrency();
        $items = '';

        foreach ($cart->contents() as $product ) {

            $items .= '<dl id="item-remove-'.$product['rowid'].'" >';
            $items .= '<dt>';
            $items .= '<img height="80" width="80" src="/'.$product['productImg'].'">';
            $items .= '<span class="dd-span-name">'.$product['name'].'</span>';
            $items .= '</dt>';
            $items .= '<dd>';
            $items .= '<span class="dd-span-action">';
            $items .= '<button id="'.$product['rowid'].'" data-url="/cart/product-remove/'.$product['rowid'] .'"
             class="btn btn-xs btn-danger pull-right hunger-remove-cart"><span class="glyphicon glyphicon-trash"></span></button>';
            $items .= '</span>';
            $items .= '<span class="dd-span-price">'.$product['price'].'x'.$product['quantity'].'='.$currency .' '. $product['price'] * $product['quantity'].'</span>';
            $items .= '</dd>';
            $items .= '</dl>';
        }
        return $items;

    }

    public function productRemoveCartAction(Request $request , $subdomain , $cartid)
    {
        $cart = new Cart($request->getSession());
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        $cart->remove($cartid);
        $cartTotal = $cart->total();
        $totalItems = $cart->total_items();
        $cartResult = $cartTotal.'('.$totalItems.')';
        $cartItems = $this->getCartItem($globalOption ,$cart);
        return new Response(json_encode(array('cartResult' => $cartResult,'cartTotal' => $cartTotal,'totalItem' => $totalItems,'cartItem' => $cartItems)));


    }

    public function productCartAction($subdomain, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=>$globalOption ,'slug' => 'basket'));

            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if($detect->isMobile() && $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }

            $cart = new Cart($request->getSession());
            return $this->render('FrontendBundle:'.$theme.':cart.html.twig',
                array(
                    'globalOption'      => $globalOption,
                    'menu'             => $menu,
                    'cart'             => $cart,
                    'pageName'          => 'Cart',
                )
            );
        }

    }

    public function productAddWishListAction($subdomain ,Item $product)
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
