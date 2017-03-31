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
use Setting\Bundle\ToolBundle\Entity\Module;
use Setting\Bundle\ToolBundle\Entity\SubscribeEmail;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Syndicate\Bundle\ComponentBundle\Entity\Education;
use Syndicate\Bundle\ComponentBundle\Entity\Vendor;

class TemplateWidgetController extends Controller
{


    public function mobileMenuAction(GlobalOption $globalOption)
    {
        $menus = $this->getDoctrine()->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=>$globalOption,'parent'=>NULL,'menuGroup'=> 1),array('sorting'=>'asc'));
        $menuTree = $this->get('setting.menuTreeSettingRepo')->getMenuTree($menus,$globalOption->getSubDomain());
        return $this->render('@Frontend/Template/Mobile/menu.html.twig', array(
            'menuTree'           => $menuTree,
        ));
    }

    public function headerAction(GlobalOption $globalOption, $pageName = '' ,Request $request)
    {
        /* Device Detection code desktop or mobile */
        $em = $this->getDoctrine()->getManager();
        $menus = $this->getDoctrine()->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=>$globalOption,'parent'=>NULL,'menuGroup'=> 1),array('sorting'=>'asc'));
        $menuTree = $this->get('setting.menuTreeSettingRepo')->getMenuTree($menus,$globalOption->getSubDomain());
        $siteEntity = $globalOption->getSiteSetting();
        $themeName = $siteEntity->getTheme()->getFolderName();

        $inventoryCat = $this->getDoctrine()->getRepository('InventoryBundle:ItemTypeGrouping')->findOneBy(array('inventoryConfig' => $globalOption->getInventoryConfig()));
        $cats = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getParentId($inventoryCat);
        $categoryTree = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getReturnCategoryTree($cats);
        return $this->render('@Frontend/Template/Desktop/'.$themeName.'/header.html.twig', array(
            'menuTree'              => $menuTree,
            'globalOption'          => $globalOption,
            'categoryTree'          => $categoryTree,
            'pageName'              => $pageName,

        ));
    }

    public function footerMenuAction(GlobalOption $globalOption, $menuGroup , Request $request)
    {
        /* Device Detection code desktop or mobile */

        $menus = $this->getDoctrine()->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=>$globalOption,'parent'=>NULL,'menuGroup'=> $menuGroup ),array('sorting'=>'asc'));
        $footerMenu = $this->get('setting.menuTreeSettingRepo')->getMenuTree($menus,$globalOption->getSubDomain());
        return $this->render('@Frontend/Template/Desktop/Widget/FooterMenu.html.twig', array(
            'footerMenu'           => $footerMenu,
        ));
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

    public function modalLoginAction(GlobalOption $globalOption)
    {

        $csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();
        $user = new User();
        $form   = $this->createCreateForm($globalOption->getSubDomain(),$user);
        return $this->render('@Frontend/Template/Desktop/Widget/modalLogin.html.twig', array(
            'globalOption'             => $globalOption,
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


    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */

    private function createCreateForm($subdomain,User $entity)
    {
        $form = $this->createForm(new CustomerRegisterType(), $entity, array(
            'action' => $this->generateUrl('webservice_customer_insert', array('subdomain' => $subdomain)),
            'method' => 'POST',
            'attr' => array(
                'id' => 'signup',
                'class' => 'register',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;

    }

    public function aboutusAction(GlobalOption $globalOption,$wordlimit)
    {

        $slug = $globalOption->getSlug().'-about-us';
        $about                     = $this->getDoctrine()->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption' => $globalOption,'slug' => $slug ));

        if(!empty($about)){
            return $this->render('@Frontend/Widget/aboutus.html.twig', array(
                'about'           => $about->getPage(),
                'wordlimit'           => $wordlimit,
            ));
        }else{
            return new Response('');
        }

    }

    public function aboutAction(GlobalOption $globalOption,$wordlimit)
    {

        $slug = $globalOption->getSlug().'-about-us';
        $about                     = $this->getDoctrine()->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption' => $globalOption,'slug' => $slug));
        if(!empty($about)){
            return $this->render('@Frontend/Widget/about.html.twig', array(
                'about'           => $about->getPage(),
                'wordlimit'           => $wordlimit,
            ));
        }else{
            return new Response('');
        }

    }

    public function moduleBaseContentAction(GlobalOption $globalOption , PageModule $pageModule )
    {
        $limit = $pageModule->getShowLimit() > 0 ? $pageModule->getShowLimit() : 5;
        $entities                    = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->findModuleContent($globalOption->getId(),$pageModule->getModule()->getId(),$limit);
        return $this->render('@Frontend/Template/Desktop/Widget/'.$pageModule->getModule()->getModuleClass().'.html.twig', array(
            'entities'           => $entities,
            'pageModule'           => $pageModule,
            'globalOption'           => $globalOption,
        ));
    }

    public function moduleSidebarBaseContentAction(GlobalOption $globalOption , Module $module )
    {
        $limit = 6;
        $entities                    = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->findModuleContent($globalOption->getId(),$module->getId(),$limit);

        return $this->render('@Frontend/Template/Desktop/Widget/sidebar.html.twig', array(
            'entities'         => $entities,
            'module'           => $module,
            'globalOption'     => $globalOption,
        ));
    }

    public function pageBaseModuleContentAction(GlobalOption $globalOption , Module $module )
    {
        $limit = 6;
        $entities                    = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->findModuleContent($globalOption->getId(),$module->getId(),$limit);

        return $this->render('@Frontend/Template/Desktop/Widget/page.html.twig', array(
            'entities'         => $entities,
            'module'           => $module,
            'globalOption'     => $globalOption,
        ));
    }





}
