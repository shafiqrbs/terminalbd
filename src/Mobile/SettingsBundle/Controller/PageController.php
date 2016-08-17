<?php

namespace Mobile\SettingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PageController extends Controller
{

    public function indexAction($subdomain,$slug){

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        if(!empty($globalOption)){

            $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption' => $globalOption ,'slug' => $slug));
            $page ='';
            $moduleName ='';
            if(!empty($menu)){

                $siteEntity = $em->getRepository('SettingToolBundle:SiteSetting')->findOneBy(array('globalOption' => $globalOption));
                $theme = $siteEntity->getTheme();
                if(!empty($siteEntity) && !empty($theme) ){
                    $themeName = $siteEntity->getTheme()->getFolderName();
                }else{
                    $themeName ='Default';
                }

                $menus = $em->getRepository('SettingAppearanceBundle:MenuGrouping')->findBy(array('globalOption'=>$globalOption,'parent'=>NULL,'menuGroup'=>6),array('sorting'=>'asc'));
                $menusTree = $this->get('setting.menuTreeSettingRepo')->getMenuTree($menus,$subdomain,'mobile');
                $moduleTheme = $this->get('setting.menuTreeSettingRepo')->getModuleTheme($menu);

                if(!empty($moduleTheme)){
                    $moduleName = $this->get('setting.menuTreeSettingRepo')->getModuleTheme($menu);
                    $twigName = "module";
                }else{
                    $page = $em->getRepository('SettingContentBundle:Page')->findOneBy(array('globalOption'=>$globalOption,'slug' => $slug));
                    $twigName = "content";
                }

            }
            $page = ($page) ? $page :'';
            return $this->render('MobileBundle:'.$themeName.':'.$twigName.'.html.twig',
                array(

                    'menu'          => $menusTree,
                    'entity'        => $globalOption,
                    'page'          => $page,
                    'moduleName'    => $moduleName
                )
            );
        }

    }

    public function emailSending(Request $request)
    {
        $data = $request->request->all();
        $this->getDoctrine()->getRepository('SettingContentBundle:ContactMessage')->insertMessage($data);
        return new Response('success');
        //$referer = $request->headers->get('referer');
        //return new RedirectResponse($referer);

    }

    public function smsSending(Request $request)
    {
        $data = $request->request->all();
        $this->getDoctrine()->getRepository('SettingContentBundle:ContactMessage')->insertMessage($data);
        return new Response('success');
        //$referer = $request->headers->get('referer');
        //return new RedirectResponse($referer);

    }

    public function blogSubmitAction(Request $request)
    {
        $data = $request->request->all();
        $this->getDoctrine()->getRepository('SettingContentBundle:BlogComment')->insertMessage($data);
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);

    }

    public function admissionSubmitAction(Request $request)
    {
        $data = $request->request->all();
        $this->getDoctrine()->getRepository('SettingContentBundle:AdmissionComment')->insertMessage($data);
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);

    }


}
