<?php

namespace Frontend\FrontentBundle\Controller;
use Core\UserBundle\Entity\User;
use Core\UserBundle\Form\CustomerRegisterType;
use Frontend\FrontentBundle\Service\Cart;
use Frontend\FrontentBundle\Service\MobileDetect;
use Product\Bundle\ProductBundle\Entity\Category;
use Setting\Bundle\ContentBundle\Entity\PageModule;
use Setting\Bundle\ToolBundle\Entity\Branding;
use Product\Bundle\ProductBundle\Entity\Product;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Entity\SubscribeEmail;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Syndicate\Bundle\ComponentBundle\Entity\Education;
use Syndicate\Bundle\ComponentBundle\Entity\Vendor;

class EcommerceWidgetController extends Controller
{

/*
    public function mobileMenuAction(GlobalOption $globalOption)
    {
        $menus = $this->getDoctrine()->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=> $globalOption,'parent'=>NULL,'menuGroup'=> 1),array('sorting'=>'asc'));
        $menuTree = $this->get('setting.menuTreeSettingRepo')->getMenuTree($menus,$globalOption->getSubDomain());
        return $this->render('@Frontend/Template/Mobile/menu.html.twig', array(
            'menuTree'           => $menuTree,
        ));
    }
*/

    public function headerAction(Request $request , GlobalOption $globalOption , $pageName ='' )
    {
        /* Device Detection code desktop or mobile */

        $siteEntity = $globalOption->getSiteSetting();
        $themeName = $siteEntity->getTheme()->getFolderName();
        $cart = new Cart($request->getSession());
        $data = $_REQUEST;
        $category = isset($data['category']) ? $data['category'] :'';

        $inventoryCat = $this->getDoctrine()->getRepository('InventoryBundle:ItemTypeGrouping')->findOneBy(array('inventoryConfig' => $globalOption->getInventoryConfig()));
        $cats = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getParentId($inventoryCat);
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $categoryTree = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getReturnCategoryTreeForMobile($cats,$category);
            $theme = 'Template/Mobile/'.$themeName;
        }else{
            $categoryTree = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getReturnCategoryTree($cats,$category);
            $theme = 'Template/Desktop/'.$themeName;
        }
        return $this->render('@Frontend/'.$theme.'/header.html.twig', array(
            'globalOption'          => $globalOption,
            'categoryTree'          => $categoryTree,
            'pageName'              => $pageName,
            'cart'                  => $cart,
            'searchForm'            => $data
        ));

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

    public function featureWidgetAction(GlobalOption $globalOption , $pageName ='', $position ='' )
    {

        $features                    = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureWidget')->findBy(array('globalOption' => $globalOption,'pageName' => $pageName ,'position' => $position ),array('sorting'=>'ASC'));
        /* Device Detection code desktop or mobile */
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/EcommerceWidget/FeatureWidget';
        }else{
            $theme = 'Template/Desktop/EcommerceWidget/FeatureWidget';
        }
        return $this->render('@Frontend/'.$theme.'.html.twig', array(
            'features'           => $features,
            'globalOption'           => $globalOption,
        ));
    }

    public function categoryWidgetAction(GlobalOption $globalOption , $category )
    {

        $data = array('category' => $category);
        $inventory = $globalOption->getInventoryConfig()->getId();
        $categoryProducts = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseVendorItem')->findFrontendProductWithSearch($inventory,$data,$limit=12);

        /* Device Detection code desktop or mobile */
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/EcommerceWidget/CategoryWidget';
        }else{
            $theme = 'Template/Desktop/EcommerceWidget/CategoryWidget';
        }
        return $this->render('@Frontend/'.$theme.'.html.twig', array(
            'categoryProducts'           => $categoryProducts->getResult(),
            'globalOption'           => $globalOption,
        ));
    }

    public function categoryShortWidgetAction(GlobalOption $globalOption)
    {


        $entities  = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureCategory')->getSliderFeatureCategory($globalOption);
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/EcommerceWidget/SliderWidget';
        }else{
            $theme = 'Template/Desktop/EcommerceWidget/SliderWidget';
        }
        return $this->render('@Frontend/'.$theme.'.html.twig', array(
            'entities'                  => $entities,
            'globalOption'              => $globalOption,
            'feature'                   => 'category',
        ));

    }

    public function brandShortWidgetAction(GlobalOption $globalOption)
    {

        $entities  = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureBrand')->getSliderFeatureBrand($globalOption);
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/EcommerceWidget/SliderWidget';
        }else{
            $theme = 'Template/Desktop/EcommerceWidget/SliderWidget';
        }
        return $this->render('@Frontend/'.$theme.'.html.twig', array(
            'entities'                  => $entities,
            'globalOption'              => $globalOption,
            'feature'                   => 'brand',
        ));

    }


    public function featureProductShortWidgetAction(GlobalOption $globalOption)
    {

        $entities  = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseVendorItem')->getSliderFeatureProduct($globalOption->getInventoryConfig());
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/EcommerceWidget/SliderWidget';
        }else{
            $theme = 'Template/Desktop/EcommerceWidget/SliderWidget';
        }
        return $this->render('@Frontend/'.$theme.'.html.twig', array(
            'entities'                  => $entities,
            'globalOption'              => $globalOption,
            'feature'                   => 'featureProduct',
        ));

    }

    public function promotionShortWidgetAction(GlobalOption $globalOption)
    {

        $entities  = $this->getDoctrine()->getRepository('SettingAppearanceBundle:Feature')->getSliderFeaturePromotion($globalOption,'Promotion');
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/EcommerceWidget/SliderWidget';
        }else{
            $theme = 'Template/Desktop/EcommerceWidget/SliderWidget';
        }
        return $this->render('@Frontend/'.$theme.'.html.twig', array(
            'entities'                  => $entities,
            'globalOption'              => $globalOption,
            'feature'                   => 'promotion',
        ));

    }

    public function tagShortWidgetAction(GlobalOption $globalOption)
    {

        $entities  = $this->getDoctrine()->getRepository('SettingAppearanceBundle:Feature')->getSliderFeaturePromotion($globalOption,'Tag');
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/EcommerceWidget/SliderWidget';
        }else{
            $theme = 'Template/Desktop/EcommerceWidget/SliderWidget';
        }
        return $this->render('@Frontend/'.$theme.'.html.twig', array(
            'entities'                  => $entities,
            'globalOption'              => $globalOption,
            'feature'                   => 'tag',
        ));

    }


    public function discountShortWidgetAction(GlobalOption $globalOption)
    {

        $entities  = $this->getDoctrine()->getRepository('SettingAppearanceBundle:Feature')->getSliderFeaturePromotion($globalOption,'Discount');
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/EcommerceWidget/SliderWidget';
        }else{
            $theme = 'Template/Desktop/EcommerceWidget/SliderWidget';
        }
        return $this->render('@Frontend/'.$theme.'.html.twig', array(
            'entities'                  => $entities,
            'globalOption'              => $globalOption,
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


}
