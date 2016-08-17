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

class WidgetController extends Controller
{

    public function indexAction()
    {
        $entities                   = $this->getDoctrine()->getRepository('SyndicateComponentBundle:Education')->getVendorList();
        $sliders                    = $this->getDoctrine()->getRepository('SettingContentBundle:SiteSlider')->findBy(array('status'=>1));
        $siteContent                = $this->getDoctrine()->getRepository('SettingContentBundle:SiteContent')->findBy(array('status'=>'1'),array('created'=>'desc'));
        $recentAdmission            = $this->getDoctrine()->getRepository('SettingContentBundle:SiteContent')->findOneBy(array('slug'=>'recent-admission','status'=>'1'));
        $brands                     = $this->getDoctrine()->getRepository('SettingToolBundle:Branding')->getBrandingSponsor();
        $news                       = $this->getDoctrine()->getRepository('SettingContentBundle:News')->findAll();
        $notice                     = $this->getDoctrine()->getRepository('SettingContentBundle:NoticeBoard')->findAll();
        $admission                  = $this->getDoctrine()->getRepository('SettingContentBundle:Admission')->findAll();
        $testimonial                = $this->getDoctrine()->getRepository('SettingContentBundle:Testimonial')->getRandomTestimonial();
        $syndicates                 = $this->getDoctrine()->getRepository('SettingToolBundle:Syndicate')->getSyndicateBaseVendor();
        $institutes                 = $this->getDoctrine()->getRepository('SettingToolBundle:InstituteLevel')->findBy(array('parent'=>NULL),array('name'=>'asc'));
        return $this->render('FrontendBundle:Default:index.html.twig', array(
            'sliders'           => $sliders,
            'entities'          => $entities,
            'siteContent'       => $siteContent,
            'recentAdmission'   => $recentAdmission,
            'brands'            => $brands,
            'newses'            => $news,
            'notices'           => $notice,
            'admissions'        => $admission,
            'testimonial'       => $testimonial,
            'syndicates'        => $syndicates,
            'institutes'        => $institutes,
        ));
    }


    public function headerMenuAction(GlobalOption $globalOption)
    {

        $menus = $this->getDoctrine()->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=>$globalOption,'parent'=>NULL,'menuGroup'=> 1),array('sorting'=>'asc'));
        $menuTree = $this->get('setting.menuTreeSettingRepo')->getMenuTree($menus,$globalOption->getSubDomain(),'desktop');

        /* Device Detection code desktop or mobile */
        $detect = new MobileDetect();
        if($detect->isMobile() && $detect->isTablet() ) {
            $theme = 'Mobile/'.$themeName;
        }else{
            $theme = 'Desktop/'.$themeName;
        }

        return $this->render('@Frontend/Widget/headerMenu.html.twig', array(
            'menuTree'           => $menuTree,
            'entity'             => $globalOption,
        ));
    }


    public function aboutusAction()
    {
        $about                     = $this->getDoctrine()->getRepository('SettingContentBundle:SiteContent')->find(1);

        return $this->render('@Frontend/Widget/aboutus.html.twig', array(
            'about'           => $about,
        ));
    }

    public function admissionAction()
    {
        $admissions                     = $this->getDoctrine()->getRepository('SettingContentBundle:Admission')->findModuleContent(5);
        return $this->render('@Frontend/Widget/admission.html.twig', array(
            'admissions'           => $admissions,
        ));
    }

    public function newsAction()
    {
        $newses                    = $this->getDoctrine()->getRepository('SettingContentBundle:Mews')->findModuleContent(5);
        return $this->render('@Frontend/Widget/news.html.twig', array(
            'newses'           => $newses,
        ));
    }

    public function noticeAction()
    {
        $notices                     = $this->getDoctrine()->getRepository('SettingContentBundle:NoticeBoard')->findBy();
        return $this->render('@Frontend/Widget/notice.html.twig', array(
            'notices'           => $notices,
        ));
    }

}
