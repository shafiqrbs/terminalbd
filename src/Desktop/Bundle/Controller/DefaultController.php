<?php

namespace Desktop\Bundle\Controller;

use Desktop\Bundle\Service\MobileDetect;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @param $subdomain
     * @return mixed
     */
    public function indexAction($subdomain)
    {



        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
;
        if(!empty($globalOption)){


            $menus = $em->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=>$globalOption,'parent'=>NULL,'menuGroup'=>1),array('sorting'=>'asc'));
            $menusTree = $this->get('setting.menuTreeSettingRepo')->getMenuTree($menus,$subdomain,'desktop');

            $siteEntity = $em->getRepository('SettingToolBundle:SiteSetting')->findOneBy(array('globalOption'=>$globalOption->getId()));

            if(!empty($siteEntity) && !empty($theme) ){
                $themeName = $siteEntity->getTheme()->getFolderName();
            }else{
                $themeName ='Default';
            }

            $homeEntity = $em->getRepository('SettingContentBundle:HomePage')->findOneBy(array('globalOption'=>$globalOption));

            $array = array();

            foreach ($globalOption->getHomesliders() as $slide)
            {
                $array[] = $slide->getWebPath();
            }
            $selectedBlockBg = rand(0, count($array)-1); // generate random number size of the array
            //$selectedBlockBg = $array[$i]; // set variable equal to which random filename was chosen

            /* Device Detection code desktop or mobile */
            $detect = new MobileDetect();
            $device = 'DesktopBundle';
            if( $detect->isMobile() && $detect->isTablet() ) {
                $device = 'MobileBundle';
            }

            return $this->render($device.':'.$themeName.':index.html.twig',
                array(

                    'menu'  => $menusTree,
                    'entity'    => $globalOption,
                    'homeEntity'    => $homeEntity,
                    'selectedBlockBg'    => $array,
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

            $menus = $em->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=>$globalOption,'parent'=>NULL,'menuGroup'=>1),array('sorting'=>'asc'));
            $menusTree = $this->get('setting.menuTreeSettingRepo')->getMenuTree($menus,$subdomain,'desktop');

            $siteEntity = $em->getRepository('SettingToolBundle:SiteSetting')->findOneBy(array('globalOption'=>$globalOption));
            $mobileTheme = $siteEntity->getMobileTheme();
            if(!empty($siteEntity) && !empty($mobileTheme) ){
                $themeName = $siteEntity->getMobileTheme()->getFolderName();
            }else{
                $themeName ='Default';
            }

            return $this->render('DesktopBundle:'.$themeName.':contact.html.twig',
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
