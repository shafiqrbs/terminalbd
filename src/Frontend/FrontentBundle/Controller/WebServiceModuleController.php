<?php

namespace Frontend\FrontentBundle\Controller;


use Frontend\FrontentBundle\Service\MobileDetect;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;

class WebServiceModuleController extends Controller
{


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

                    $page = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=>$globalOption,'slug' => $module));
                    $twigName = "content";
                    $featurePages = $em->getRepository('SettingContentBundle:Page')->getListForModule($globalOption,$page->getPage());
                }
            }

        }

        $pagination = ($pagination) ? $pagination :'';
        $page = ($page) ? $page->getPage() :'';

        /* Device Detection code desktop or mobile */
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
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


    public function moduleCategoryAction($subdomain,$module,$slug)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        if(!empty($globalOption)){

            $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=>$globalOption ,'slug' => $module));

            $page ='';
            $pagination ='';
            $moduleName ='';

            if(!empty($menu)){

                $siteEntity = $globalOption->getSiteSetting();
                $themeName = $siteEntity->getTheme()->getFolderName();

                $moduleName = $this->get('setting.menuTreeSettingRepo')->getCheckModule($menu);
                if($moduleName){
                    $twigName = "module";
                    $cat = $em->getRepository('SettingContentBundle:ModuleCategory')->findOneBy(array('globalOption'=>$globalOption ,'slug' => $slug));
                    $pagination = $em->getRepository('SettingContentBundle:Page')->getCategoryPage($globalOption,$menu->getModule(),$cat);
                    $pagination = $this->paginate( $pagination,$limit= 10 );
                    if(!empty($menu->getModule())){
                        $categories = $em->getRepository('SettingContentBundle:ModuleCategory')->moduleBaseCategory($globalOption->getId(),$menu->getModule()->getId());
                    }

                }else{

                    $page = $em->getRepository('SettingContentBundle:Page')->findOneBy(array('globalOption'=>$globalOption,'slug' => $module));
                    $twigName = "content";

                }
            }

        }

        $pagination = ($pagination) ? $pagination :'';
        $page = ($page) ? $page :'';

        /* Device Detection code desktop or mobile */

        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/'.$themeName;
        }else{
            $theme = 'Template/Desktop/'.$themeName;
        }
        return $this->render('FrontendBundle:'.$theme.':'.$twigName.'.html.twig',
            array(

                'globalOption'  => $globalOption,
                'module'        => $menu->getModule(),
                'categories'    => $categories,
                'title'         => $cat->getName(),
                'pagination'    => $pagination,
                'page'          => $page,
            )
        );
    }



    public function modulePageAction($subdomain,$module,$slug)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        if(!empty($globalOption)){

            $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=>$globalOption ,'slug' => $module));

            $categories ='';
            $page ='';
            $moduleName ='';
            $details = "";
            if(!empty($menu)){

                $siteEntity = $globalOption->getSiteSetting();
                $themeName = $siteEntity->getTheme()->getFolderName();

                $moduleName = $this->get('setting.menuTreeSettingRepo')->getModuleTheme($menu);
                if($moduleName){

                    $details = $em->getRepository('SettingContentBundle:Page')->findOneBy(array('globalOption'=>$globalOption,'slug' => $slug));
                    $twigName = "moduleDetails";
                    if(!empty($menu->getModule())){
                        $categories = $em->getRepository('SettingContentBundle:ModuleCategory')->moduleBaseCategory($globalOption->getId(),$menu->getModule()->getId());
                    }

                }else{

                    /** @pram $page Page */
                    $page = $em->getRepository('SettingContentBundle:Page')->findOneBy(array('globalOption'=>$globalOption,'slug' => $module));
                    $featurePages = $em->getRepository('SettingContentBundle:Page')->getListForModule($globalOption,$page);
                    $twigName = "content";

                }
            }

        }


        $page = ($page) ? $page :'';
        $categories = ($categories) ? $categories :'';
        /* Device Detection code desktop or mobile */
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/'.$themeName;
        }else{
            $theme = 'Template/Desktop/'.$themeName;
        }
        return $this->render('FrontendBundle:'.$theme.':'.$twigName.'.html.twig',
            array(
                'globalOption'          => $globalOption,
                'categories'            => $categories,
                'page'                  => $page,
                'moduleName'            => $moduleName,
                'details'               => $details,
            )
        );
    }

    public function contactAction($subdomain){

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        if(!empty($globalOption)){

            $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=>$globalOption ,'slug' => 'contact'));
            $page ='';
            if(!empty($menu)){
                $siteEntity = $globalOption->getSiteSetting();
                $themeName = $siteEntity->getTheme()->getFolderName();
            }

        }

        $branches = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->findBy(array('status'=>1,'globalOption'=>$globalOption,'module'=>6),array('name'=>'ASC'));
        /* Device Detection code desktop or mobile */
        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/'.$themeName;
        }else{
            $theme = 'Template/Desktop/'.$themeName;
        }
        return $this->render('FrontendBundle:'.$theme.':contact.html.twig',
            array(
                'globalOption'        => $globalOption,
                'branches'        => $branches,
              )
        );

    }

    public function contentAction($subdomain){

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));

        if(!empty($globalOption)){

            $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=>$globalOption ,'slug' => 'contact'));
            $page ='';
            $moduleName ='';

            if(!empty($menu)){

                $siteEntity = $globalOption->getSiteSetting();

                if(!empty($siteEntity)){
                    $themeName = $siteEntity->getTheme()->getFolderName();
                }else{
                    $themeName ='Default';
                }

                $menus = $em->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=>$globalOption,'parent'=>NULL,'menuGroup'=> 1),array('sorting'=>'asc'));
                $menusTree = $this->get('setting.menuTreeSettingRepo')->getMenuTree($menus,$subdomain,'desktop');

                $page = $em->getRepository('SettingContentBundle:Page')->findOneBy(array('globalOption'=>$globalOption,'slug' => $module));
                $twigName = "content";

            }

        }

        $page = ($page) ? $page :'';

        /* Device Detection code desktop or mobile */

        $detect = new MobileDetect();
        if( $detect->isMobile() ||  $detect->isTablet() ) {
            $theme = 'Template/Mobile/'.$themeName;
        }else{
            $theme = 'Template/Desktop/'.$themeName;
        }
        return $this->render('FrontendBundle:'.$theme.':content.html.twig',
            array(

                'globalOption'  => $globalOption,
                'page'          => $page,
                'moduleName'    => $moduleName
            )
        );

    }

    public function paginate($entities,$limit = 10)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            $limit /*limit per page*/
        );
        return $pagination;
    }




}
