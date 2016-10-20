<?php
namespace Bindu\BinduBundle\Controller;
use Frontend\FrontentBundle\Service\MobileDetect;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MobileController extends Controller
{
    /**
     * @param $subdomain
     * @return mixed
     */
    public function indexAction($subdomain)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        if(!empty($globalOption)){


            $siteEntity = $globalOption->getSiteSetting();
            if(!empty($siteEntity) && !empty($theme) ){
                $themeName = $siteEntity->getTheme()->getFolderName();
            }else{
                $themeName ='Default';
            }
            /* Device Detection code desktop or mobile */

            $menus = $this->getDoctrine()->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=>$globalOption,'parent'=>NULL,'menuGroup'=> 1),array('sorting'=>'asc'));
            $menuTree = $this->get('setting.menuTreeSettingRepo')->getMobileMenuTree($menus,$subdomain);
            return $this->render('FrontendBundle:Template/Mobile/MobilePreview:index.html.twig',
                array(
                    'themeName'    => $themeName,
                    'entity'    => $globalOption,
                    'menuTree'    => $menuTree,
                    'globalOption'    => $globalOption,
               )
            );

        }else{

            return $this->redirect($this->generateUrl('homepage'));
        }
    }


    public function contentAction($subdomain,$slug){

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        if(!empty($globalOption)){

            $siteEntity = $globalOption->getSiteSetting();
            if(!empty($siteEntity) && !empty($theme) ){
                $themeName = $siteEntity->getTheme()->getFolderName();
            }else{
                $themeName ='Default';
            }
            /* Device Detection code desktop or mobile */

            $menus = $this->getDoctrine()->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=>$globalOption,'parent'=>NULL,'menuGroup'=> 1),array('sorting'=>'asc'));
            $menuTree = $this->get('setting.menuTreeSettingRepo')->getMobileMenuTree($menus,$subdomain);
            $content = $this->getDoctrine()->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=>$globalOption,'slug'=>$slug));
            return $this->render('FrontendBundle:Template/Mobile/MobilePreview:content.html.twig',
                array(
                    'themeName'    => $themeName,
                    'page'    => $content->getPage(),
                    'menuTree'    => $menuTree,
                    'globalOption'    => $globalOption,
                )
            );

        }


    }

    public function contactAction($subdomain){


        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        if(!empty($globalOption)){

            $menus = $em->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=>$globalOption,'parent'=>NULL,'menuGroup'=>1),array('sorting'=>'asc'));
            $menusTree = $this->get('setting.menuTreeSettingRepo')->getMobileMenuTree($menus,$subdomain);

            $siteEntity = $em->getRepository('SettingToolBundle:SiteSetting')->findOneBy(array('globalOption'=>$globalOption));
            $mobileTheme = $siteEntity->getMobileTheme();
            if(!empty($siteEntity) && !empty($mobileTheme) ){
                $themeName = $siteEntity->getMobileTheme()->getFolderName();
            }else{
                $themeName ='Default';
            }

            return $this->render('FrontendBundle:Template/Mobile/MobilePreview:contact.html.twig',
                array(

                    'menu'  => $menusTree,
                    'entity'    => $globalOption,
                    'globalOption'    => $globalOption,
                    'menuTree'    => $menusTree

                )
            );
        }

    }
    public function mapsAction($subdomain){


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
            return $this->render('MobileBundle:'.$themeName.':maps.html.twig',
                array(

                    'menu'  => $menusTree,
                    'entity'    => $globalOption

                )
            );
        }

    }

    public function smsAction($subdomain){


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
            return $this->render('MobileBundle:'.$themeName.':sms.html.twig',
                array(

                    'menu'  => $menusTree,
                    'entity'    => $globalOption

                )
            );
        }

    }


    public function moduleAction($subdomain,$module)
    {


        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        if(!empty($globalOption)){

            $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=>$globalOption ,'slug' => $module));

            $categories ='';
            $page ='';
            $pagination ='';
            $moduleName ='';
            $featurePages ='';
            if(!empty($menu)){

                $siteEntity = $globalOption->getSiteSetting();
                $themeName = $siteEntity->getTheme()->getFolderName();
                $moduleName = $this->get('setting.menuTreeSettingRepo')->getCheckModule($menu);
                if($moduleName){
                    $twigName = "module";
                    $pagination = $em->getRepository('SettingContentBundle:Page')->findBy(array('globalOption'=>$globalOption,'module'=>$menu->getModule()->getId(),'status'=>1),array('id'=>'desc'));
                    $pagination = $this->paginate( $pagination,$limit= 10 );
                    if(!empty($menu->getModule())){
                        $categories = $em->getRepository('SettingContentBundle:ModuleCategory')->moduleBaseCategory($globalOption->getId(),$menu->getModule()->getId());
                    }

                }else{

                    $page = $em->getRepository('SettingContentBundle:Page')->findOneBy(array('globalOption'=>$globalOption,'slug' => $module));
                    $twigName = "content";
                    $featurePages = $em->getRepository('SettingContentBundle:Page')->getListForModule($globalOption,$page);
                }
            }

        }

        $pagination = ($pagination) ? $pagination :'';
        $page = ($page) ? $page :'';

        /* Device Detection code desktop or mobile */
        $detect = new MobileDetect();
        if( $detect->isMobile() &&  $detect->isTablet() ) {
            $theme = 'Template/Mobile/'.$themeName;
        }else{
            $theme = 'Template/Desktop/'.$themeName;
        }
        return $this->render('FrontendBundle:'.$theme.':'.$twigName.'.html.twig',
            array(

                'globalOption'  => $globalOption,
                'module'        => $menu->getModule(),
                'categories'    => $categories,
                'title'         => $moduleName,
                'pagination'    => $pagination,
                'page'          => $page,
                'featurePages'  => $featurePages,
            )
        );
    }


    public function moduleContentAction($subdomain,$module,$id){

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingContentBundle:'.$module)->find($id);

        echo '<img class="responsive-image" src="/'.$entity->getWebpath().'">';
        echo $entity->getContent();
        exit;

    }


}
