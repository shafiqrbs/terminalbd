<?php

namespace Mobile\SettingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction($subdomain)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        if(!empty($globalOption)){


            $menus = $em->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=>$globalOption,'parent'=>NULL,'menuGroup'=>6),array('sorting'=>'asc'));
            $menusTree = $this->get('setting.menuTreeSettingRepo')->getMenuTree($menus,$subdomain);

            $siteEntity = $em->getRepository('SettingToolBundle:SiteSetting')->findOneBy(array('globalOption'=>$globalOption));

            $mobileTheme = $siteEntity->getTheme();
            if(!empty($siteEntity) && !empty($mobileTheme) ){
                $themeName = $siteEntity->getTheme()->getFolderName();
            }else{
                $themeName ='Default';
            }

            $homeEntity = $em->getRepository('SettingContentBundle:HomePage')->findOneBy(array('globalOption'=>$globalOption));
            $isIntro = $globalOption->getIsIntro();

            if($isIntro == 1 ){
                $page = 'index';
            }else{
                $page = 'home';
            }
            return $this->render('MobileBundle:'.$themeName.':'.$page.'.html.twig',
                array(

                    'menu'  => $menusTree,
                    'entity'    => $globalOption,
                )
            );

        }else{

            return $this->redirect($this->generateUrl('homepage'));

        }
    }

    public function homeAction($subdomain){

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));

        if(!empty($globalOption)){

            $menus = $em->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('parent'=>NULL,'menuGroup'=> 6 ),array('sorting'=>'asc'));
            $menusTree = $this->get('setting.menuTreeSettingRepo')->getMenuTree($menus,$subdomain);

            $siteEntity = $em->getRepository('SettingToolBundle:SiteSetting')->findOneBy(array('globalOption'=>$globalOption));
            $mobileTheme = $siteEntity->getMobileTheme();
            if(!empty($siteEntity) && !empty($mobileTheme) ){
                $themeName = $siteEntity->getMobileTheme()->getFolderName();
            }else{
                $themeName ='Default';
            }

            return $this->render('MobileBundle:'.$themeName.':home.html.twig',
                array(
                    'menu'  => $menusTree,
                    'entity'    => $globalOption
                )
            );
        }

    }

    public function contactAction($subdomain){


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

            return $this->render('MobileBundle:'.$themeName.':contact.html.twig',
                array(

                    'menu'  => $menusTree,
                    'entity'    => $globalOption

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



    public function moduleContentAction($subdomain,$module,$id){

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingContentBundle:'.$module)->find($id);

        echo '<img class="responsive-image" src="/'.$entity->getWebpath().'">';
        echo $entity->getContent();
        exit;

    }
}
