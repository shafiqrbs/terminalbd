<?php

namespace Frontend\FrontentBundle\Controller;
use Product\Bundle\ProductBundle\Entity\Category;
use Setting\Bundle\ToolBundle\Entity\Branding;
use Product\Bundle\ProductBundle\Entity\Product;
use Setting\Bundle\ToolBundle\Entity\SubscribeEmail;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Syndicate\Bundle\ComponentBundle\Entity\Education;
use Syndicate\Bundle\ComponentBundle\Entity\Vendor;

class EcommerceController extends Controller
{


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

    public function indexAction()
    {

        $globalOption               = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->find(2);
        $entities                   = $this->getDoctrine()->getRepository('SyndicateComponentBundle:Education')->getVendorList();
        $sliders                    = $this->getDoctrine()->getRepository('SettingContentBundle:SiteSlider')->findBy(array('status'=>1),array('id'=>'DESC'));
        $siteContent                = $this->getDoctrine()->getRepository('SettingContentBundle:SiteContent')->findBy(array('status'=>'1'),array('created'=>'desc'));
           return $this->render('FrontendBundle:Main/Ecommerce:index.html.twig', array(
            'globalOption'           => $globalOption,
            'entities'          => $entities,
            'siteContent'       => $siteContent,
            'sliders'       => $sliders,

        ));
    }



    public function searchAction()
    {

        return $this->render('FrontendBundle:Default:search.html.twig');
    }

    public function getCaptcha()
    {
       // session_start();
        $text = rand(10000,99999);
        $_SESSION["vercode"] = $text;
        $height = 25;
        $width = 65;

        $image_p = imagecreate($width, $height);
        $black = imagecolorallocate($image_p, 0, 107, 143);
        $white = imagecolorallocate($image_p, 255, 255, 255);
        $font_size = 14;

        imagestring($image_p, $font_size, 5, 5, $text, $white);
        imagejpeg($image_p, null, 80);
    }

}
