<?php

namespace Frontend\FrontentBundle\Controller;
use Frontend\FrontentBundle\Service\MobileDetect;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class WebServiceController extends Controller
{
    /**
     * @param $subdomain
     * @return mixed
     */
    public function indexAction($subdomain)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('status'=>1,'subDomain'=>$subdomain));

        if(!empty($globalOption)){


            $siteEntity = $globalOption->getSiteSetting();
            $themeName = $siteEntity->getTheme()->getFolderName();
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
            if( $detect->isMobile() ||  $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }
            return $this->render('FrontendBundle:'.$theme.':index.html.twig',
                array(
                    'entity'    => $globalOption,
                    'globalOption'    => $globalOption,
                    'homeEntity'    => $homeEntity,
                    'selectedBlockBg'    => $array,
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
