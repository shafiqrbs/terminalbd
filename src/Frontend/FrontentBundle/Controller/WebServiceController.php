<?php

namespace Frontend\FrontentBundle\Controller;
use Frontend\FrontentBundle\Service\Cart;
use Frontend\FrontentBundle\Service\MobileDetect;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

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
        	if($globalOption->getDomainType() == 'ecommerce'){
		        return $this->renderEcommerce($request , $globalOption);
	        }elseif ($globalOption->getDomainType() == 'medicine'){
		        return $this->renderMedicine($request , $globalOption);
	        }else{
		        return $this->renderWebsite($globalOption);
	        }
        }else{
            return $this->redirect($this->generateUrl('homepage'));
        }
    }

	protected function renderEcommerce(Request $request ,GlobalOption $globalOption)
	{

		$em = $this->getDoctrine()->getManager();
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

	}
    public function renderWebsite(GlobalOption $globalOption){

	    $em = $this->getDoctrine()->getManager();
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
			    'entity'            => $globalOption,
			    'globalOption'      => $globalOption,
			    'pageName'          => 'Home',
			    'menu'              => $menu,
			    'searchForm'    => array(),
		    )
	    );
    }

    public function renderMedicine(Request $request , GlobalOption $globalOption){

	    $em = $this->getDoctrine()->getManager();
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
    }


    public function moduleContentAction($subdomain,$module,$id){

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingContentBundle:'.$module)->find($id);
        echo '<img class="responsive-image" src="/'.$entity->getWebpath().'">';
        echo $entity->getContent();
        exit;

    }


    public function frontendLogoutAction(Request $request)
    {
        $this->get('security.context')->setToken(null);
        $this->get('request')->getSession()->invalidate();
        return new Response('success');

    }


}
