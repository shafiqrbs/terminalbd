<?php

namespace Frontend\FrontentBundle\Controller;
use Appstore\Bundle\EcommerceBundle\Entity\Discount;
use Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig;
use Appstore\Bundle\EcommerceBundle\Entity\Item;
use Appstore\Bundle\EcommerceBundle\Entity\Promotion;
use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Appstore\Bundle\EcommerceBundle\Entity\ItemBrand;
use Frontend\FrontentBundle\Form\EcommerceProductEditType;

use Core\UserBundle\Entity\User;
use Frontend\FrontentBundle\Service\Cart;
use Frontend\FrontentBundle\Service\MobileDetect;
use Product\Bundle\ProductBundle\Entity\Category;
use Setting\Bundle\AppearanceBundle\Entity\FeatureWidget;
use Setting\Bundle\AppearanceBundle\Entity\Menu;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EcommerceWidgetController extends Controller
{


    public function cookieBaseProductListAction(Request $request)
    {
        $btnActive = $_REQUEST['btnActive'];
        $cookie = new Cookie('btnActiveList', $btnActive);
        $response = new Response();
        $value = $response->headers->setCookie($cookie);
        $request->cookies->get($cookie['btnActiveList']['value']);
    }

    public function mobileMenuAction(GlobalOption $globalOption)
    {
        return $this->render('@Frontend/Template/Mobile/Widget/ecommerceMenu.html.twig', array(
            'globalOption'          => $globalOption,
        ));
    }


    public function headerAction(Request $request , GlobalOption $globalOption , Menu $menu )
    {
        /* Device Detection code desktop or mobile */

        $siteEntity = $globalOption->getSiteSetting();
        $themeName = $siteEntity->getTheme()->getFolderName();
        $cart = new Cart($request->getSession());
        $data = $_REQUEST;
	    $categoryTree = '';
        $selected = isset($data['category']) ? $data['category'] :'';
        $config = $globalOption->getEcommerceConfig();
        $cats = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getParentId($config);
        $categoryTree = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getCategoryTreeForMobile($cats,$selected);
       // $searchForm = $this->createCreateForm(new Item(),$globalOption);
        $detect = new MobileDetect();
        $brandTree = $this->getDoctrine()->getRepository('EcommerceBundle:ItemBrand')->findBy(array('ecommerceConfig'=> $globalOption->getEcommerceConfig(),'status' => 1));
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/'.$themeName;
        }else{
            $theme = 'Template/Desktop/'.$themeName;
        }

        return $this->render('@Frontend/'.$theme.'/header.html.twig', array(
            'globalOption'          => $globalOption,
        //    'form'                  => $searchForm->createView(),
            'categoryTree'          => $categoryTree,
            'brandTree'             => $brandTree,
            'menu'                  => $menu,
            'cart'                  => $cart,
        ));

    }


    /**
     * Creates a form to create a Item entity.
     *
     * @param Item $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Item $entity,GlobalOption $global)
    {

        $config = $global->getEcommerceConfig();
        $em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new EcommerceProductEditType($em,$config), $entity, array(
            'action' => $this->generateUrl("{$global->getSubDomain()}_webservice_product_search"),
            'method' => 'GET',
            'attr' => array(
                'class' => 'action bs-example',
                'novalidate' => 'novalidate',
                'data-example-id' => "input-group-segmented-buttons"
            )
        ));
        return $form;
    }


    public function returnMegaCategoryMenuAction(GlobalOption $globalOption , $categories,$column = 6){

        $categoryMegaMenu =  $this->getDoctrine()->getRepository('SettingAppearanceBundle:EcommerceMenu')->getMegaMenuCategory($globalOption,$categories,$column);

        return new Response($categoryMegaMenu);
    }

    public function returnSimpleCategoryMenuAction($categories){

        $categoryMegaMenu =  $this->getDoctrine()->getRepository('SettingAppearanceBundle:EcommerceMenu')->getSimpleCategoryMenu($categories);

        return new Response($categoryMegaMenu);
    }

    public function footerAction(GlobalOption $globalOption,Request $request)
    {

        $menus = $this->getDoctrine()->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=>$globalOption,'parent'=>NULL,'menuGroup'=> 1),array('sorting'=>'asc'));
        $footerMenu = $this->get('setting.menuTreeSettingRepo')->getFooterMenu($menus,$globalOption->getSubDomain(),'desktop');

        $csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();
        $user = new User();
        $form   = $this->createCreateForm($globalOption->getSubDomain(),$user);

        $cart = new Cart($request->getSession());
        $cartTotal = $cart->total();
        $totalItems = $cart->total_items();
        $cartResult = $cartTotal.'('.$totalItems.')';

        return $this->render('@Frontend/Template/Desktop/footer.html.twig', array(
            'globalOption'             => $globalOption,
            'footerMenu'               => $footerMenu,
            'cartResult'               => $cartResult,
            'csrfToken'   => $csrfToken,
            'form'   => $form->createView(),
        ));
    }

    public function sidebarAction(GlobalOption $globalOption,Request $request)
    {

        $cart = new Cart($request->getSession());
        $cartTotal = $cart->total();
        $totalItems = $cart->total_items();
        $cartResult = $cartTotal.'('.$totalItems.')';
        return $this->render('@Frontend/Template/Desktop/sidebar.html.twig', array(
            'globalOption'             => $globalOption,
            'cartResult'               => $cartResult,
        ));
    }

    public function sidebarTemplateProductFilterAction(Request $request ,GlobalOption $globalOption , $searchForm = array() )
    {
        $cart = new Cart($request->getSession());
        if(!empty($globalOption)) {

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();

            /* @var EcommerceConfig $inventory */

            $inventory = $globalOption->getEcommerceConfig();

            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();

            if ($detect->isMobile() || $detect->isTablet()) {
                $theme = 'Template/Mobile/'.$themeName.'/EcommerceWidget';
            } else {
                $theme = 'Template/Desktop/'.$themeName.'/EcommerceWidget';
            }
            $post = $_REQUEST;
            $catSelected = isset($post['category']) ? $post['category']:array();
            $data['category']= isset($post['categories']) ? $post['categories']:array();
            $data['brand']= isset($post['brands']) ? $post['brands']:array();
            $data['tag']= isset($post['tags']) ? $post['tags']:array();
            $data['promotion']= isset($post['promotions']) ? $post['promotions']:array();
            $data['discount']= isset($post['discounts']) ? $post['discounts']:array();


            $cats = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getParentId($inventory->getId());
            $categorySidebar = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->productCategorySidebar($cats, $data['category']);
            $categoryTree = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getCategoryTreeForMobile($cats,$catSelected);
            $brandTree = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->findGroupBrands($inventory, $data['brand']);
            $discountTree = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->findGroupDiscount($inventory, $data['discount']);
            $promotionTree = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->findPromotionTree($inventory,$data['promotion']);
            $tagTree = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->findTagTree($inventory,$data['tag']);

        }

        return $this->render('@Frontend/'.$theme.'/productFilter.html.twig', array(

                'globalOption'              => $globalOption,
                'categorySidebar'           => $categorySidebar,
                'categoryTree'              => $categoryTree,
                'brandTree'                 => $brandTree,
                'colorTree'                 => '',
                'sizeTree'                  => '',
                'discountTree'              => $discountTree,
                'promotionTree'             => $promotionTree,
                'tagTree'                   => $tagTree,
                'searchForm'                => $searchForm,
                'cart' => $cart,

            )
        );

    }

    public function sidebarProductFilterAction(GlobalOption $globalOption , $searchForm = array() )
    {


    }

    public function footerProductCategoryAction(GlobalOption $globalOption)
    {

        if(!empty($globalOption)) {

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();

            /* @var EcommerceConfig $inventory */

            $inventory = $globalOption->getEcommerceConfig();

            /* Device Detection code desktop or mobile */

            $theme = 'Template/Desktop/'.$themeName.'/EcommerceWidget';
            $cats = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getParentId($inventory->getId());
           // $categorySidebar = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->footerProductCategory($cats);

        }

        return $this->render('@Frontend/'.$theme.'/footerCategory.html.twig', array(

                'globalOption'              => $globalOption,
                'categories'                => $cats,


            )
        );

    }

    public function footerProductChildCategoryAction(GlobalOption $globalOption, Category $category)
    {

        if(!empty($globalOption)) {

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();

            /* @var EcommerceConfig $inventory */

            $inventory = $globalOption->getEcommerceConfig();

            /* Device Detection code desktop or mobile */

            $theme = 'Template/Desktop/'.$themeName.'/EcommerceWidget';
            $child = $category->getChildren();
            $categorySidebar = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->footerProductCategory($child);
        }

        return $this->render('@Frontend/'.$theme.'/footerSubCategory.html.twig', array(
                'categorySidebar'           => $categorySidebar,
            )
        );

    }

    public function aboutusAction(GlobalOption $globalOption,$wordlimit)
    {

        $about                     = $this->getDoctrine()->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption' => $globalOption,'slug' => 'about-us'));
        if(!empty($about)){
            return $this->render('@Frontend/Widget/aboutus.html.twig', array(
                'about'           => $about->getPage(),
                'wordlimit'           => $wordlimit,
            ));
        }else{
            return new Response('');
        }

    }

    public function featureWidgetAction(GlobalOption $globalOption , $menu ='', $position ='' )
    {

        $features = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureWidget')->findBy(array('globalOption' => $globalOption,'widgetFor'=>'e-commerce', 'menu' => $menu  ,'position' => $position ), array('sorting'=>'ASC'));

        /* Device Detection code desktop or mobile */

        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/EcommerceWidget/FeatureWidget';
        }else{
            $theme = 'Template/Desktop/EcommerceWidget/FeatureWidget';
        }

        return $this->render('@Frontend/'.$theme.'.html.twig', array(
            'features'                  => $features,
            'globalOption'              => $globalOption,
        ));
    }

    public function categoryWidgetAction(GlobalOption $globalOption , $category , $sliderId = 1  )
    {

        $data = array('category' => $category);
        $inventory = $globalOption->getInventoryConfig()->getId();
        $categoryProducts = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->findFrontendProductWithSearch($inventory,$data,$limit = 12);

        /* Device Detection code desktop or mobile */
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/EcommerceWidget/CategoryWidget';
        }else{
            $theme = 'Template/Desktop/EcommerceWidget/CategoryWidget';
        }
        return $this->render('@Frontend/'.$theme.'.html.twig', array(
            'categoryProducts'          => $categoryProducts->getResult(),
            'globalOption'              => $globalOption,
            'sliderId'                  => $sliderId,
        ));
    }

    public function categoryShortWidgetAction(GlobalOption $globalOption , $position)
    {

        $config = $globalOption->getEcommerceConfig();
        $entities  = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getFeatureCategory($config,12);
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/EcommerceWidget/SliderWidget';
        }else{
            $theme = 'Template/Desktop/EcommerceWidget/SliderWidget';
        }
        return $this->render('@Frontend/'.$theme.'.html.twig', array(
            'entities'                  => $entities,
            'globalOption'              => $globalOption,
            'position'                  => $position,
            'feature'                   => 'category',
        ));

    }

    public function brandShortWidgetAction(GlobalOption $globalOption , $position)
    {
        $config = $globalOption->getEcommerceConfig();
        $entities  = $this->getDoctrine()->getRepository('EcommerceBundle:ItemBrand')->getFeatureBrand($config,8);
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/EcommerceWidget/SliderWidget';
        }else{
            $theme = 'Template/Desktop/EcommerceWidget/SliderWidget';
        }
        return $this->render('@Frontend/'.$theme.'.html.twig', array(
            'entities'                  => $entities,
            'globalOption'              => $globalOption,
            'position'                   => $position,
            'feature'                   => 'brand',
        ));

    }

    public function featureProductShortWidgetAction(GlobalOption $globalOption,$position)
    {

        $entities  = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getSliderFeatureProduct($globalOption->getInventoryConfig());
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/EcommerceWidget/SliderWidget';
        }else{
            $theme = 'Template/Desktop/EcommerceWidget/SliderWidget';
        }
        return $this->render('@Frontend/'.$theme.'.html.twig', array(
            'entities'                  => $entities,
            'globalOption'              => $globalOption,
            'position'                  => $position,
            'feature'                   => 'featureProduct',
        ));

    }

    public function promotionShortWidgetAction(GlobalOption $globalOption ,$position)
    {

        $config = $globalOption->getEcommerceConfig();
        $entities  = $this->getDoctrine()->getRepository('EcommerceBundle:Promotion')->getFeaturePromotion($config,8);
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/EcommerceWidget/SliderWidget';
        }else{
            $theme = 'Template/Desktop/EcommerceWidget/SliderWidget';
        }
        return $this->render('@Frontend/'.$theme.'.html.twig', array(
            'entities'                  => $entities,
            'globalOption'              => $globalOption,
            'position'                   => $position,
            'feature'                   => 'promotion',
        ));

    }

    public function tagShortWidgetAction(GlobalOption $globalOption,$position)
    {

        $config = $globalOption->getEcommerceConfig();
        $entities  = $this->getDoctrine()->getRepository('EcommerceBundle:Promotion')->getFeatureTag($config,8);
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/EcommerceWidget/SliderWidget';
        }else{
            $theme = 'Template/Desktop/EcommerceWidget/SliderWidget';
        }
        return $this->render('@Frontend/'.$theme.'.html.twig', array(
            'entities'                  => $entities,
            'globalOption'              => $globalOption,
            'position'              => $position,
            'feature'                   => 'tag',
        ));

    }

    public function discountShortWidgetAction(GlobalOption $globalOption,$position)
    {

        $config = $globalOption->getEcommerceConfig();
        $entities  = $this->getDoctrine()->getRepository('EcommerceBundle:Discount')->getFeatureDiscount($config,8);
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/EcommerceWidget/SliderWidget';
        }else{
            $theme = 'Template/Desktop/EcommerceWidget/SliderWidget';
        }
        return $this->render('@Frontend/'.$theme.'.html.twig', array(
            'entities'                  => $entities,
            'globalOption'              => $globalOption,
            'position'                   => $position,
            'feature'                   => 'discount',
        ));

    }

    public function homeTopWidgetAction(GlobalOption $globalOption , $position='' )
    {

        $ecommerce = $globalOption->getEcommerceConfig()->getId();
        $templates                    = $this->getDoctrine()->getRepository('EcommerceBundle:Template')->findBy(array('ecommerceConfig' => $ecommerce),array('sorting'=>'ASC'));

        return $this->render('@Frontend/Template/Desktop/EcommerceWidget/Template.html.twig', array(
            'templates'           => $templates,
        ));
    }

    public function footerMenuAction(GlobalOption $globalOption, $menuGroup , Request $request)
    {
        /* Device Detection code desktop or mobile */

        $menus = $this->getDoctrine()->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=>$globalOption,'parent'=>NULL,'menuGroup'=> $menuGroup ),array('sorting'=>'asc'));
        $footerMenu = $this->get('setting.menuTreeSettingRepo')->getEcommerceFooterMenuTree($menus,$globalOption->getSubDomain());
        return $this->render('@Frontend/Template/Desktop/Widget/FooterMenu.html.twig', array(
            'footerMenu'           => $footerMenu,
        ));
    }


    /* =================================================Template Base Widget===================*/

    public function featureTemplateWidgetAction(GlobalOption $globalOption , $menu ='', $position ='' )
    {

        $features                    = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureWidget')->findBy(array('globalOption' => $globalOption, 'menu' => $menu  ,'position' => $position ), array('sorting'=>'ASC'));
        $siteEntity = $globalOption->getSiteSetting();
        $themeName = $siteEntity->getTheme()->getFolderName();
        /* Device Detection code desktop or mobile */

        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/'.$themeName.'/EcommerceWidget/FeatureWidget';
        }else{
            $theme = 'Template/Desktop/'.$themeName.'/EcommerceWidget/FeatureWidget';
        }


        return $this->render('@Frontend/'.$theme.'.html.twig', array(
            'features'                  => $features,
            'globalOption'              => $globalOption,
        ));
    }

     /* =================================================Template Base Widget===================*/

    public function featureTemplateMobileWidgetAction(GlobalOption $globalOption , $menu = '', $position ='' )
    {

        $siteEntity = $globalOption->getSiteSetting();
        $themeName = $siteEntity->getTheme()->getFolderName();
        $features                    = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureWidget')->findBy(array('globalOption' => $globalOption, 'menu' => $menu  ,'position' => $position ), array('sorting'=>'ASC'));
        return $this->render("@Frontend/Template/Mobile/{$themeName}/EcommerceWidget/FeatureWidget.html.twig", array(
            'features'                  => $features,
            'globalOption'              => $globalOption,
        ));
    }

    public function ecommerceMobileWidgetAction(GlobalOption $globalOption , Menu $menu , $position ='' )
    {

        $features                    = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureWidget')->findBy(array('globalOption' => $globalOption, 'widgetFor'=>'e-commerce','menu' => $menu,'position' => $position ),array('sorting'=>'ASC'));
        return $this->render('@Frontend/Template/Mobile/EcommerceWidget/feature.html.twig', array(
            'features'                  => $features,
            'globalOption'            => $globalOption,
        ));
    }

    public function sliderMobileFeatureWidgetAction(GlobalOption $globalOption , FeatureWidget $widget)
    {

        return $this->render('@Frontend/Template/Mobile/EcommerceWidget/feature.html.twig', array(
            'widget'                => $widget,
            'globalOption'          => $globalOption

        ));
    }

    public function FeatureBrandWidgetAction(GlobalOption $globalOption , FeatureWidget $widget)
    {

        $limit = $widget->getFeatureBrandLimit() > 0 ? $widget->getFeatureBrandLimit():8;
        $entities                    = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureBrand')->getSliderFeatureBrand($globalOption,$limit);
        return $this->render('@Frontend/Template/Mobile/EcommerceWidget/brandWidget.html.twig', array(
            'entities'              => $entities,
            'widget'                => $widget,
            'globalOption'          => $globalOption
        ));
    }

    public function FeatureCategoryWidgetAction(GlobalOption $globalOption , FeatureWidget $widget)
    {

        $limit = $widget->getCategoryLimit() > 0 ? $widget->getCategoryLimit():8;
        $entities                    = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureCategory')->getSliderFeatureCategory($globalOption,$limit);
        return $this->render('@Frontend/Template/Mobile/EcommerceWidget/categoryWidget.html.twig', array(
            'entities'              => $entities,
            'widget'                => $widget,
            'globalOption'          => $globalOption
        ));
    }

    public function ecommerceMobileFeatureWidgetAction(GlobalOption $globalOption , FeatureWidget $widget)
    {

        $limit = $widget->getModuleShowLimit() > 0 ? $widget->getModuleShowLimit() : 10;
        $entities                    = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->findModuleContent($globalOption->getId(), $widget->getModule() ,$limit);
        return $this->render('@Frontend/Template/Mobile/EcommerceWidget/page.html.twig', array(
            'entities'              => $entities,
            'widget'                => $widget,
            'globalOption'          => $globalOption
        ));
    }

    public function featureCategoryTemplateWidgetAction(GlobalOption $globalOption , FeatureWidget $widget)
    {

        $datalimit = $widget->getBrandLimit();
        $limit = $datalimit > 0 ? $datalimit : 12;
        $config = $globalOption->getEcommerceConfig()->getId();
        $categories = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getFeatureCategory($config,$limit);
        $siteEntity = $globalOption->getSiteSetting();
        $themeName = $siteEntity->getTheme()->getFolderName();
        /* Device Detection code desktop or mobile */
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/'.$themeName.'/EcommerceWidget/FeatureCategoryWidget';
        }else{
            $theme = 'Template/Desktop/'.$themeName.'/EcommerceWidget/FeatureCategoryWidget';
        }
        return $this->render('@Frontend/'.$theme.'.html.twig', array(
            'widget'                  => $widget,
            'categories'              => $categories,
            'globalOption'            => $globalOption,

        ));
    }


    public function categoryTemplateWidgetAction(GlobalOption $globalOption , FeatureWidget $widget, Category $category)
    {

        $datalimit = $widget->getCategoryLimit();
        $limit = $datalimit > 0 ? $datalimit : 12;
        $config = $globalOption->getEcommerceConfig()->getId();
        $featureCategory = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureCategory')->findOneBy(array('globalOption' => $globalOption, 'category' => $category));

        $products = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getFeatureCategoryProduct($config, $category->getId(),$limit);
        $siteEntity = $globalOption->getSiteSetting();
        $themeName = $siteEntity->getTheme()->getFolderName();

        /* Device Detection code desktop or mobile */

        $detect = new MobileDetect();

        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/'.$themeName.'/EcommerceWidget';
        }else{
            $theme = 'Template/Desktop/'.$themeName.'/EcommerceWidget';
        }

        return $this->render('@Frontend/'.$theme.'/CategoryWidget.html.twig', array(
            'products'                  => $products->getResult(),
            'globalOption'              => $globalOption,
            'widget'                    => $widget,
            'featureCategory'           => $featureCategory,
            'category'                  => $category,
        ));
    }

    public function featureBrandTemplateWidgetAction(GlobalOption $globalOption , FeatureWidget $widget)
    {

        $datalimit = $widget->getBrandLimit();
        $limit = $datalimit > 0 ? $datalimit : 12;
        $config = $globalOption->getEcommerceConfig()->getId();
        $brands = $this->getDoctrine()->getRepository('EcommerceBundle:ItemBrand')->getFeatureBrand($config,$limit);
        $siteEntity = $globalOption->getSiteSetting();
        $themeName = $siteEntity->getTheme()->getFolderName();
        /* Device Detection code desktop or mobile */
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/'.$themeName.'/EcommerceWidget/FeatureBrandWidget';
        }else{
            $theme = 'Template/Desktop/'.$themeName.'/EcommerceWidget/FeatureBrandWidget';
        }
        return $this->render('@Frontend/'.$theme.'.html.twig', array(
            'widget'                  => $widget,
            'brands'                  => $brands,
            'globalOption'            => $globalOption,

        ));
    }

    public function brandTemplateWidgetAction(GlobalOption $globalOption , FeatureWidget $widget, ItemBrand $brand)
    {

        $datalimit = $widget->getBrandLimit();
        $limit = $datalimit > 0 ? $datalimit : 12;
        $config = $globalOption->getEcommerceConfig()->getId();
        $products = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getFeatureBrandProduct($config, $brand->getId(),$limit);
        $siteEntity = $globalOption->getSiteSetting();
        $themeName = $siteEntity->getTheme()->getFolderName();
        /* Device Detection code desktop or mobile */
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/'.$themeName.'/EcommerceWidget/BrandWidget';
        }else{
            $theme = 'Template/Desktop/'.$themeName.'/EcommerceWidget/BrandWidget';
        }
        return $this->render('@Frontend/'.$theme.'.html.twig', array(
            'products'                  => $products->getResult(),
            'globalOption'              => $globalOption,
            'widget'                    => $widget,
            'brand'                     => $brand,
        ));
    }

    public function promotionTemplateWidgetAction(GlobalOption $globalOption , FeatureWidget $widget, Promotion $promotion)
    {

        $datalimit = $widget->getBrandLimit();
        $limit = $datalimit > 0 ? $datalimit : 12;
        $config = $globalOption->getEcommerceConfig()->getId();
        $products = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getFeaturePromotionProduct($config, $promotion->getId(),$limit);
        $siteEntity = $globalOption->getSiteSetting();
        $themeName = $siteEntity->getTheme()->getFolderName();
        /* Device Detection code desktop or mobile */
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/'.$themeName.'/EcommerceWidget/PromotionWidget';
        }else{
            $theme = 'Template/Desktop/'.$themeName.'/EcommerceWidget/PromotionWidget';
        }
        return $this->render('@Frontend/'.$theme.'.html.twig', array(
            'products'                  => $products->getResult(),
            'globalOption'              => $globalOption,
            'widget'                    => $widget,
            'promotion'                     => $promotion,
        ));
    }

    public function tagTemplateWidgetAction(GlobalOption $globalOption , FeatureWidget $widget, Promotion $tag)
    {

        $datalimit = $widget->getBrandLimit();
        $limit = $datalimit > 0 ? $datalimit : 12;
        $config = $globalOption->getEcommerceConfig()->getId();
        $products = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getFeatureTagProduct($config, $tag->getId(),$limit);
        $siteEntity = $globalOption->getSiteSetting();
        $themeName = $siteEntity->getTheme()->getFolderName();
        /* Device Detection code desktop or mobile */
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/'.$themeName.'/EcommerceWidget/TagWidget';
        }else{
            $theme = 'Template/Desktop/'.$themeName.'/EcommerceWidget/TagWidget';
        }
        return $this->render('@Frontend/'.$theme.'.html.twig', array(
            'products'                  => $products->getResult(),
            'globalOption'              => $globalOption,
            'widget'                    => $widget,
            'tag'                     => $tag,
        ));
    }
    public function discountTemplateWidgetAction(GlobalOption $globalOption , FeatureWidget $widget, Discount $discount)
    {

        $datalimit = $widget->getBrandLimit();
        $limit = $datalimit > 0 ? $datalimit : 12;
        $config = $globalOption->getEcommerceConfig()->getId();
        $products = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getFeatureDiscountProduct($config, $discount->getId(),$limit);
        $siteEntity = $globalOption->getSiteSetting();
        $themeName = $siteEntity->getTheme()->getFolderName();
        /* Device Detection code desktop or mobile */
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/'.$themeName.'/EcommerceWidget/DiscountWidget';
        }else{
            $theme = 'Template/Desktop/'.$themeName.'/EcommerceWidget/DiscountWidget';
        }
        return $this->render('@Frontend/'.$theme.'.html.twig', array(
            'products'                  => $products->getResult(),
            'globalOption'              => $globalOption,
            'widget'                    => $widget,
            'discount'                  => $discount,
        ));
    }

}
