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

            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if( $detect->isMobile() || $detect->isTablet() ) {
                $pagination = $this->paginate($entities, $limit,"nextPrevDropDown");
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $pagination = $this->paginate($entities, $limit,$globalOption->getTemplateCustomize()->getPagination());
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

    public function productFilterAction(Request $request , $subdomain)
    {
        $cart = new Cart($request->getSession());
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));

        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=> $globalOption ,'slug' => 'product'));

            $post = $_REQUEST;
            $data['webName']= isset($post['webName']) ? $post['webName']:'';
            $data['category']= isset($post['category']) ? $post['category']:'';
            $data['categories']= isset($post['categories']) ? $post['categories']:'';
            $data['brand']= isset($post['brands']) ? $post['brands']:'';
            $data['tag']= isset($post['tags']) ? $post['tags']:'';
            $data['promotion']= isset($post['promotions']) ? $post['promotions']:'';
            $data['discount']= isset($post['discounts']) ? $post['discounts']:'';
            $config = $globalOption->getEcommerceConfig();
            $limit = !empty($data['limit'])  ? $data['limit'] : $config->getPerPage();
            $entities = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->filterFrontendProductWithSearch($config->getId(),$data);

            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if( $detect->isMobile() || $detect->isTablet() ) {
                $pagination = $this->paginate($entities, $limit,"nextPrevDropDown");
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $pagination = $this->paginate($entities, $limit,$globalOption->getTemplateCustomize()->getPagination());
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

    public function productStockSearchAction($subdomain)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        $item = trim($_REQUEST['q']);
        $search_arr = array();
        if ($item) {
            $config = $globalOption->getEcommerceConfig();
            $items = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->searchWebStock($item,$config);
            foreach ($items as $item):
                $id = $item['id'];
                $name = $item['text'];
                $search_arr[] = array("id" => $id, "name" => $name);
            endforeach;
        }
        return new Response(json_encode($search_arr));
    }

    public function productSearchAction(Request $request , $subdomain)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        if(!empty($globalOption)){
            return $this->medicineProductSearch($request , $subdomain);
        }else{
            return $this->redirect($this->generateUrl('homepage'));
        }
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
         //   $entities =$rwaEntities->getResult();
            $medicineConfig = $globalOption->getMedicineConfig()->getId();
           // $vendorEntities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->findWithSearch($medicineConfig,$data);
         //  $globalEntities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->getMedicineBrandSearch($data);

            /* Device Detection code desktop or mobile */
            $config = $globalOption->getEcommerceConfig();
            $limit = !empty($data['limit'])  ? $data['limit'] : $config->getPerPage();
            $detect = new MobileDetect();
            if( $detect->isMobile() || $detect->isTablet() ) {
                $pagination = $this->paginate($entities, $limit,"nextPrevDropDown");
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $pagination = $this->paginate($entities, $limit,$globalOption->getTemplateCustomize()->getPagination());
                $theme = 'Template/Desktop/'.$themeName;
            }

            $item = !empty($_REQUEST) ? $_REQUEST :array();
            $searchForm =$item;
            return $this->render('FrontendBundle:'.$theme.':productSearch.html.twig',
                array(
                    'globalOption'      => $globalOption,
                    'cart'              => $cart,
                    'products'          => $pagination,
                 //   'vendorEntities'    => $vendorEntities,
                  //  'globalEntities'    => $globalEntities,
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
                $pagination = $this->paginate($entities, $limit,"nextPrevDropDown");
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $pagination = $this->paginate($entities, $limit,$globalOption->getTemplateCustomize()->getPagination());
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
                $pagination = $this->paginate($entities, $limit,"nextPrevDropDown");
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $pagination = $this->paginate($entities, $limit,$globalOption->getTemplateCustomize()->getPagination());
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
                $pagination = $this->paginate($entities, $limit,"nextPrevDropDown");
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $pagination = $this->paginate($entities, $limit,$globalOption->getTemplateCustomize()->getPagination());
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
                $pagination = $this->paginate($entities, $limit,"nextPrevDropDown");
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $pagination = $this->paginate($entities, $limit,$globalOption->getTemplateCustomize()->getPagination());
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
                $pagination = $this->paginate($entities, $limit,"nextPrevDropDown");
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $pagination = $this->paginate($entities, $limit,$globalOption->getTemplateCustomize()->getPagination());
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
                    'subItem'           => '',
                    'next'              => $next,
                    'previous'          => $previous,
                    'menu'              => $menu,
                    'searchForm'        => $searchForm,
                    'pageName'          => 'ProductDetails',
                )
            );
        }
    }

    public function stockItemDetailsAction($subdomain)
    {
        $id = $_REQUEST['stockId'];
        $entity = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->find($id);
        $price = $entity->getSalesPrice();
        $unit = empty($entity->getProductUnit()) ? '' : $entity->getProductUnit()->getName();
        $subItems = "";
        if($entity->getSubProduct() == 1 and count($entity->getItemSubs()) > 0){
            $subItems .= "<select class='form-control modalChangeSubItem' id='size' name='size' data-url='/product-subitem-modal/{$entity->getId()}'>";
            foreach ($entity->getItemSubs() as  $sub):
                $subUnit = empty($sub->getProductUnit()) ? '' : $sub->getProductUnit()->getName();
                $subItems .="<option  value='{$sub->getId()}' >{$sub->getSize()->getName()} - {$subUnit}</option>";
            endforeach;
            $subItems .= '</select>';
        }
        return new Response(json_encode(array('price' => $price , 'unit' => $unit, 'subItems' => $subItems)));

    }

    public function productModalAction(Request $request ,$subdomain,Item $item)
    {
        $cart = new Cart($request->getSession());
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
                    'cart'              => $cart,
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

            $array = (json_encode(array('subItem' => $html ,'subItemQuantity' => $subItem->getQuantity())));
            return new Response($array);
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
            $unit = empty($subItem->getProductUnit()) ? '' : $subItem->getProductUnit()->getName();
            if($subItem->getDiscountPrice()){
                $price = "<strike>{$subItem->getSalesPrice()}</strike> <strong class='list-price' >{$subItem->getDiscountPrice()}</strong>/{$unit}";
            }else{
                $price = "<strong class='list-price'>{$subItem->getSalesPrice()}</strong>/{$unit}";
            }

            $array = (json_encode(array('subItem' => $html ,'salesPrice' => $price )));
            return new Response($array);

        }
    }

    public function modalSubItemAction($subdomain , Item $product)
    {

        $em = $this->getDoctrine()->getManager();
        $subItem = $_REQUEST['subItem'];
        $subItem = $em->getRepository('EcommerceBundle:ItemSub')->findOneBy(array('item'=>$product,'id'=>$subItem));
        $colors = "";
        if(count($subItem->getColors()) > 0 ){
            $colors .='<div class="col-xs-3 col-md-3 pull-right">
                            <div class="form-group">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon" id="sizing-addon3"><span class="glyphicon glyphicon-th"></span></span>
                                    <select class="form-control" name="color" id="color" >';
            foreach ($subItem->getColors() as  $color):
                $colors .="<option  value='' >{$color->getName()}</option>";
            endforeach;
            $colors .='</select></div></div></div>';
        }

        $unit = empty($subItem->getProductUnit()) ? '' : $subItem->getProductUnit()->getName();
        if($subItem->getDiscountPrice()){
            $price = $subItem->getDiscountPrice();
        }else{
            $price =$subItem->getSalesPrice();
        }
        $array = (json_encode(array('colors' => $colors ,'salesPrice' => $price,'unit' => $unit )));
        return new Response($array);


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

    private function returnCartSummaryAjaxData($cart)
    {
        $amount = number_format($cart->total(), 2, '.', '');
        $data = array(
            'cartTotal' =>  (string)$amount,
            'totalItems' => count($cart->contents()),
            'totalQuantity' => (string)$cart->total_items(),
            'cartResult' => count($cart->contents())." | ৳ ".(string)$amount,
            'process' => "success"
        );
        $array = json_encode($data);
        return $array;
    }

    public function productAddCartAction(Request $request , $subdomain , Item $product, ItemSub $subitem)
    {

        $cart = new Cart($request->getSession());
        $em = $this->getDoctrine()->getManager();

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
            $array = $this->returnCartSummaryAjaxData($cart);
        }else{
            $array =(json_encode(array('process'=>'invalid')));
        }
       return new Response($array);

    }

    public function productInlineCartAction(Request $request , $subdomain , Item $product)
    {

        $data = $_REQUEST;

        $cart = new Cart($request->getSession());
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain' => $subdomain));

        $productImg = !empty($data['productImg']) ? $data['productImg'] : '';
        $quantity = !empty($data['quantity']) ? $data['quantity'] : 0;
        $sub = !empty($data['subItem']) ? $data['subItem'] : 0;
        $color = !empty($data['color']) ? $data['color'] : 0;

        if ($color > 0) {
            $colorName = $this->getDoctrine()->getRepository('SettingToolBundle:ProductColor')->find($color)->getName();
        } else {
            $colorName = '';
        }

        if ($sub > 0) {
            $subitem = $this->getDoctrine()->getRepository('EcommerceBundle:ItemSub')->find($sub);
        } else {
            $subitem = '';
        }

        /** @var  $globalOption GlobalOption */

        if (!empty($subitem)) {

            $salesPrice = $subitem->getDiscountPrice() == null ?  $subitem->getSalesPrice() : $subitem->getDiscountPrice();
            $unit = empty($subitem->getProductUnit()) ? '' : '-'.$subitem->getProductUnit()->getName();
            $size = $subitem->getSize()->getName();
            $sizeUnit = $size.$unit;
            $insert = array(
                'id' => $subitem->getId(),
                'name' => $product->getWebName(),
                'brand' => !empty($product->getBrand()) ? $product->getBrand()->getName() : '',
                'category' => !empty($product->getCategory()) ? $product->getCategory()->getName() : '',
                'productUnit' => $sizeUnit,
                'color' => $colorName,
                'colorId' => $color,
                'price' => $salesPrice,
                'quantity' => $quantity,
                'productImg' => $productImg);
            $cart->insert($insert);

        }else{

            $salesPrice = $product->getDiscountPrice() == null ?  $product->getSalesPrice() : $product->getDiscountPrice();
            $insert = array(
                'id' => $product->getId(),
                'name' => $product->getWebName(),
                'brand' => !empty($product->getBrand()) ? $product->getBrand()->getName() : '',
                'category' => !empty($product->getCategory()) ? $product->getCategory()->getName() : '',
                'size' => !empty($product->getSize()) ? $product->getSize()->getName() : '',
                'sizeUnit' => !empty($product->getProductUnit()) ? $product->getProductUnit()->getName() : '',
                'productUnit' => !empty($product->getProductUnit()) ? $product->getProductUnit()->getName() : '',
                'price' => $salesPrice,
                'quantity' => $quantity,
                'productImg' => $productImg
            );
            $cart->insert($insert);
        }
        $array = $this->returnCartSummaryAjaxData($cart);
        return new Response($array);

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
            $array = $this->returnCartSummaryAjaxData($cart);
        }else{
            $array =(json_encode(array('process'=>'invalid')));
        }
        return new Response($array);

    }

    public function productAddMedicineCartAction(Request $request , $subdomain , $product)
    {

        $cart = new Cart($request->getSession());
        $em = $this->getDoctrine()->getManager();
        $ecommerceItem = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->find($product);
        $stockItem = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->find($product);
        $productId = '';
        $productName = '';
        $productUnit = '';
        $brand = '';
        $category = '';
        $salesPrice = 0;
        if($ecommerceItem){
            $productId = $ecommerceItem->getId();
            $productName = $ecommerceItem->getWebName();
            $productUnit = (!empty($ecommerceItem->getProductUnit())) ? $ecommerceItem->getProductUnit()->getName() : '';
            $brand = !empty($ecommerceItem->getBrand()) ? $ecommerceItem->getBrand()->getName() : '';
            $category = !empty($ecommerceItem->getCategory()) ? $ecommerceItem->getCategory()->getName() : '';
            $salesPrice = $ecommerceItem->getDiscountPrice() == null ?  $ecommerceItem->getSalesPrice() : $ecommerceItem->getDiscountPrice();
        }elseif($stockItem){
            $productId = $stockItem->getId();
            $productName = $stockItem->getName();
            $productUnit = (!empty($stockItem->getUnit())) ? $stockItem->getUnit()->getName() : '';
            $brand = !empty($stockItem->getBrandName()) ? $stockItem->getBrandName() : '';
            $salesPrice = $stockItem->getSalesPrice();
        }
        $data = $_REQUEST;
        $quantity =  isset($data['quantity']) ? $data['quantity'] :'';

        /** @var GlobalOption $globalOption */

            $data = array(
                'id' => $productId,
                'name' => $productName,
                'brand' => $brand,
                'category' => $category,
                'price' => $salesPrice,
                'quantity' => $quantity,
                'productUnit' => $productUnit,
                'productImg' => ''
            );

            $cart->insert($data);
            $array = $this->returnCartSummaryAjaxData($cart);
            return new Response($array);

    }

    public function stockPreviewAction(Request $request , $subdomain)
    {

        $cart = new Cart($request->getSession());
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain' => $subdomain));
        $detect = new MobileDetect();
        $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
        if($detect->isMobile() || $detect->isTablet() ) {
            $theme = "Template/Mobile/{$themeName}/EcommerceWidget";
        }else{
            $theme = "Template/Desktop/{$themeName}/EcommerceWidget";
        }
        $html = $this->renderView(
            'FrontendBundle:'.$theme.':Cart.html.twig', array(
                'cart' => $cart,
                'globalOption' => $globalOption
            )
        );
        return new Response($html);

    }

    public function cartPrintAction(Request $request , $subdomain)
    {
        $mobile = $_REQUEST['mobile'];
        $cart = new Cart($request->getSession());
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain' => $subdomain));
        $detect = new MobileDetect();
        $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
        if($detect->isMobile() || $detect->isTablet() ) {
            $theme = "Template/Mobile/{$themeName}/EcommerceWidget";
        }else{
            $theme = "Template/Desktop/{$themeName}/EcommerceWidget";
        }
        $html = $this->renderView(
            'FrontendBundle:'.$theme.':CartPrint.html.twig', array(
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
            /* @var $product Item */
            $product = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->find($data['stockId']);
            $quantity = isset($data['itemQuantity']) ? $data['itemQuantity'] : '';
            $subItem = isset($data['size']) ? $data['size'] : '';

            /** @var GlobalOption $globalOption */
            if(empty($subItem) and empty($product)){
                $a = mt_rand(1000,9999);
                $itemName = isset($data['itemName']) ? $data['itemName'] : '';
                $insert = array(
                    'id' => $a,
                    'name' => $itemName,
                    'brand' => '',
                    'category' => '',
                    'price' => '',
                    'quantity' => $quantity,
                    'productUnit' => '',
                    'productImg' => ''
                );
                $cart->insert($insert);

            }elseif(empty($subItem) and !empty($product)){
                $productUnit = (!empty($product->getProductUnit())) ? $product->getProductUnit()->getName() : '';
                $insert = array(
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'brand' => empty($product->getBrand()) ? '' : $product->getBrand()->getName(),
                    'category' => empty($product->getCategory()) ? '' : $product->getCategory()->getName(),
                    'price' => $product->getSalesPrice(),
                    'quantity' => $quantity,
                    'productUnit' => $productUnit,
                    'productImg' => ''
                );
                $cart->insert($insert);

            }elseif(!empty($subItem)){

                $subitem = $this->getDoctrine()->getRepository('EcommerceBundle:ItemSub')->find($subItem);
                $salesPrice = $subitem->getDiscountPrice() == null ?  $subitem->getSalesPrice() : $subitem->getDiscountPrice();
                $unit = empty($subitem->getProductUnit()) ? '' : '-'.$subitem->getProductUnit()->getName();
                $size = $subitem->getSize()->getName();
                $sizeUnit = $size.$unit;
                $insert = array(
                    'id' => $subitem->getId(),
                    'name' => $product->getWebName(),
                    'brand' => !empty($product->getBrand()) ? $product->getBrand()->getName() : '',
                    'category' => !empty($product->getCategory()) ? $product->getCategory()->getName() : '',
                    'productUnit' => $sizeUnit,
                    'price' => $salesPrice,
                    'quantity' => $quantity,
                    'productImg' => '');
                $cart->insert($insert);
            }
        }
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain' => $subdomain));
        $detect = new MobileDetect();
        $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
        if($detect->isMobile() || $detect->isTablet() ) {
            $theme = "Template/Mobile/{$themeName}/EcommerceWidget";
        }else{
            $theme = "Template/Desktop/{$themeName}/EcommerceWidget";
        }
        $html = $this->renderView(
            'FrontendBundle:'.$theme.':stockCart.html.twig', array(
                'cart' => $cart,
                'globalOption' => $globalOption
            )
        );
        return new Response($html);

    }

    public function stockItemToCartAction(Request $request , $subdomain)
    {

        $cart = new Cart($request->getSession());
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain' => $subdomain));
        $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
        $data = $request->request->all();

        if(!empty($data)) {

            $product = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->find($data['stockId']);
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
            $theme = "Template/Mobile/{$themeName}/";
        }else{
            $theme = "Template/Desktop/{$themeName}/";
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
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain' => $subdomain));
        $detect = new MobileDetect();
        $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
        if($detect->isMobile() || $detect->isTablet() ) {
            $theme = "Template/Mobile/{$themeName}/EcommerceWidget/";
        }else{
            $theme = "Template/Desktop/{$themeName}/EcommerceWidget/";
        }
        $html = $this->renderView(
            'FrontendBundle:'.$theme.':Cart.html.twig', array(
                'cart' => $cart,
                'globalOption' => $globalOption
            )
        );
        return new Response($html);
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
            $array = $this->returnCartSummaryAjaxData($cart);

        }else{
            $array =(json_encode(array('process'=>'invalid')));
        }
        return new Response($array);

    }

    public function productMedicineUpdateCartAction(Request $request , $cartid)
    {
        $em = $this->getDoctrine()->getManager();
        $cart = new Cart($request->getSession());
        $quantity = (int)$_REQUEST['quantity'];
        $data = array(
            'rowid' => $cartid,
            'quantity' => $quantity,
        );

        $cart->update($data);
        $array = $this->returnCartSummaryAjaxData($cart);
        return new Response($array);
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
        $array = $this->returnCartSummaryAjaxData($cart);
        return new Response($array);
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
