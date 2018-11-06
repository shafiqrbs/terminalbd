<?php

namespace Frontend\FrontentBundle\Controller;
use Frontend\FrontentBundle\Service\Cart;
use Frontend\FrontentBundle\Service\MobileDetect;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class WebServiceController extends Controller
{
    /**
     * @param $subdomain
     * @return mixed
     */
    public function indexAction(Request $request , $subdomain)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));

        if(!empty($globalOption)){

            $cart = new Cart($request->getSession());
            $siteEntity = $globalOption->getSiteSetting();
            $themeName = $siteEntity->getTheme()->getFolderName();
            $menu = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption' => $globalOption ,'slug' => 'home'));

            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if( $detect->isMobile() ||  $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }

            return $this->render('FrontendBundle:'.$theme.':index.html.twig',
                array(
                    'entity'    => $globalOption,
                    'globalOption'    => $globalOption,
                    'pageName'    => 'Home',
                    'menu'    => $menu,
                    'cart'    => $cart,
                    'searchForm'    => array(),
                )
            );

        }else{

            return $this->redirect($this->generateUrl('homepage'));
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
