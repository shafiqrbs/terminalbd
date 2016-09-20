<?php

namespace Frontend\FrontentBundle\Controller;
use Frontend\FrontentBundle\Service\MobileDetect;
use Product\Bundle\ProductBundle\Entity\Category;
use Setting\Bundle\ToolBundle\Entity\Branding;
use Product\Bundle\ProductBundle\Entity\Product;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Entity\SubscribeEmail;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Syndicate\Bundle\ComponentBundle\Entity\Education;
use Syndicate\Bundle\ComponentBundle\Entity\Vendor;

class TemplateWidgetController extends Controller
{


    public function headerAction(GlobalOption $globalOption)
    {

        $siteEntity = $globalOption->getSiteSetting();
        $themeName = $siteEntity->getTheme()->getFolderName();

        /* Device Detection code desktop or mobile */

        $detect = new MobileDetect();
        $menus = $this->getDoctrine()->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=>$globalOption,'parent'=>NULL,'menuGroup'=> 1),array('sorting'=>'asc'));
        $menuTree = $this->get('setting.menuTreeSettingRepo')->getMenuTree($menus,$globalOption->getSubDomain());

        if($detect->isMobile() && $detect->isTablet()) {
            $theme = 'Mobile/'.$themeName;
        }else{
            $theme = 'Desktop/'.$themeName;
        }

        return $this->render('@Frontend/Template/'.$theme.'/header.html.twig', array(
            'menuTree'           => $menuTree,
            'globalOption'             => $globalOption,
        ));
    }

    public function footerAction(GlobalOption $globalOption)
    {
        $siteEntity = $globalOption->getSiteSetting();
        $themeName = $siteEntity->getTheme()->getFolderName();

        /* Device Detection code desktop or mobile */

        $detect = new MobileDetect();
        if($detect->isMobile() && $detect->isTablet() ) {
            $theme = 'Mobile/'.$themeName;
        }else{
            $theme = 'Desktop/'.$themeName;
        }
        $menus = $this->getDoctrine()->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=>$globalOption,'parent'=>NULL,'menuGroup'=> 1),array('sorting'=>'asc'));
        $footerMenu = $this->get('setting.menuTreeSettingRepo')->getFooterMenu($menus,$globalOption->getSubDomain(),'desktop');

        return $this->render('@Frontend/Template/'.$theme.'/footer.html.twig', array(
            'globalOption'             => $globalOption,
            'footerMenu'               => $footerMenu,
        ));
    }

    public function aboutusAction(GlobalOption $globalOption,$wordlimit)
    {
        $slug = $globalOption->getSlug().'-about-us';
        $about                     = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->findOneBy(array('globalOption' => $globalOption,'slug' => $slug));
        return $this->render('@Frontend/Widget/aboutus.html.twig', array(
            'about'           => $about,
            'wordlimit'           => $wordlimit,
        ));
    }


}
