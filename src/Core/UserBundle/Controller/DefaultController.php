<?php

namespace Core\UserBundle\Controller;

use Doctrine\Common\Util\Debug;
use Setting\Bundle\ToolBundle\Entity\AppModule;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        return $this->render('UserBundle:Default:index.html.twig', array());
    }

    public function landingAction()
    {

        $user = $this->getUser();

        $globalOption = $user->getGlobalOption();
        if( $user->getGlobalOption()){
            $enable =$globalOption->getStatus();
        }else{
            $enable = 0;
        }

        $apps = array();
        if (!empty($globalOption ->getSiteSetting())) {

        	$modules = $globalOption->getSiteSetting()->getAppModules();

            /* @var AppModule $mod */

            foreach ($modules as $mod) {
                if (!empty($mod->getModuleClass())) {
                    $apps[] = $mod->getSlug();
                }
            }
        }
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_DOMAIN') && $enable != 1) {
            return $this->redirect($this->generateUrl('bindu_build'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_RESTAURANT') && $enable == 1 and in_array('restaurant',$apps)) {
            return $this->redirect($this->generateUrl('restaurant_homepage'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_DMS') && $enable == 1 && in_array('dms',$apps)) {
            return $this->redirect($this->generateUrl('dms_homepage'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_DPS') && $enable == 1 && in_array('dps',$apps)) {
            return $this->redirect($this->generateUrl('dps_homepage'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_HOSPITAL') && $enable == 1 && in_array('hms',$apps) ) {
            return $this->redirect($this->generateUrl('hospital_homepage'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_MEDICINE') && $enable == 1 && in_array('miss',$apps) ) {
            return $this->redirect($this->generateUrl('medicine_homepage'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_BUSINESS') && $enable == 1 && in_array('business',$apps) ) {
            return $this->redirect($this->generateUrl('business_homepage'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_HOTEL') && $enable == 1 && in_array('hotel',$apps) ) {
            return $this->redirect( $this->generateUrl('hotel_homepage'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_ELECTION') && $enable == 1 && in_array('election',$apps) ) {
        	return $this->redirect($this->generateUrl('election_homepage'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_INVENTORY') && $enable == 1 ) {
        	return $this->redirect($this->generateUrl('inventory_homepage'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_WEBSITE') && $enable == 1) {
	        return $this->redirect($this->generateUrl('website'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_DOMAIN') && $enable == 1) {
	        return $this->redirect($this->generateUrl('domain'));
/*        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_DOMAIN_INVENTORY_SALES') && $enable == 1) {
            $inventory = $user->getGlobalOption()->getInventoryConfig();
            $deliveryProcess = $inventory->getDeliveryProcess();
            if (!empty($deliveryProcess)) {
                if ('pos' == $deliveryProcess) {
                    return $this->redirect($this->generateUrl('inventory_sales'));
                } elseif ('general-sales' == $deliveryProcess) {
                    return $this->redirect($this->generateUrl('inventory_salesonline'));
                } elseif ('manual-sales' == $deliveryProcess) {
                    return $this->redirect($this->generateUrl('inventory_salesmanual'));
                }
            }
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_DOMAIN_INVENTORY_PURCHASE') && $enable == 1) {
            return $this->redirect($this->generateUrl('purchase'));*/
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_AGENT')) {
              return $this->redirect($this->generateUrl('agentclient'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_CUSTOMER')) {
              return $this->redirect($this->generateUrl('customer'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_DOMAIN_ACCOUNTING') && $enable == 1 ) {
            return $this->redirect($this->generateUrl('account_transaction_cash_overview'));
        }elseif (!empty($user) && $enable == 2 ) {
            return $this->redirect($this->generateUrl('domain_pendig'));
        }elseif (!empty($user) && $enable == 3 ) {
            return $this->redirect($this->generateUrl('domain_suspended'));
        }else{
            return $this->redirect($this->generateUrl('bindu_homepage'));
        }

    }

    public function lockAction(){

        return $this->render('UserBundle:Default:lock.html.twig', array());
    }

    public function adminAction()
    {
        $user = $this->getUser();
        $em = $this->get('doctrine.orm.entity_manager');
        return $this->render('UserBundle:Default:admin.html.twig', array(
            'user' => $user
        ));

    }

    public function userAction()
    {
        $user = $this->getUser();
        $em = $this->get('doctrine.orm.entity_manager');

        return $this->render('UserBundle:Default:admin.html.twig', array(
            'user' => $user
        ));

    }
    public function domainAction()
    {

        /* @var GlobalOption $globalOption */
        $globalOption = $this->getUser()->getGlobalOption();
        $modules = $globalOption->getSiteSetting()->getAppModules();
        $apps = array();
        if (!empty($globalOption ->getSiteSetting()) and !empty($modules)) {
            /* @var AppModule $mod */
            foreach ($modules as $mod) {
                if (!empty($mod->getModuleClass())) {
                    $apps[] = $mod->getSlug();
                }
            }
        }
        return $this->render('UserBundle:Default:domain.html.twig', array(
            'globalOption' => $globalOption,
             'apps' => $apps
        ));
    }

    public function websiteAction()
    {
        /* @var GlobalOption $globalOption */
        $globalOption = $this->getUser()->getGlobalOption();
        $modules = $globalOption->getSiteSetting()->getAppModules();
        $apps = array();
        if (!empty($globalOption ->getSiteSetting()) and !empty($modules)) {
            /* @var AppModule $mod */
            foreach ($modules as $mod) {
                if (!empty($mod->getModuleClass())) {
                    $apps[] = $mod->getSlug();
                }
            }
        }
        return $this->render('UserBundle:Default:domain.html.twig', array(
            'globalOption' => $globalOption,
            'apps' => $apps
        ));

    }

    public function hospitalAction()
    {
        /* @var GlobalOption $globalOption */
        $globalOption = $this->getUser()->getGlobalOption();
        $modules = $globalOption->getSiteSetting()->getAppModules();
        $apps = array();
        if (!empty($globalOption ->getSiteSetting()) and !empty($modules)) {
            /* @var AppModule $mod */
            foreach ($modules as $mod) {
                if (!empty($mod->getModuleClass())) {
                    $apps[] = $mod->getSlug();
                }
            }
        }
        return $this->render('UserBundle:Default:domain.html.twig', array(
            'globalOption' => $globalOption,
            'apps' => $apps
        ));

    }


    public function pendingAction()
    {
        $user = $this->getUser();
        return $this->render('UserBundle:Default:pending.html.twig', array(
            'user' => $user,
        ));
    }

    public function suspendedAction()
    {
        $user = $this->getUser();
        return $this->render('UserBundle:Default:suspended.html.twig', array(
            'user' => $user,
        ));
    }

    public function vendorAction()
    {

        $user = $this->getUser();
        $em = $this->get('doctrine.orm.entity_manager');
        return $this->render('UserBundle:Default:domain.html.twig', array(
            'user' => $user
        ));
    }
    public function tutorAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        return $this->render('UserBundle:Default:index.html.twig', array());
    }

    public function generalAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        return $this->render('UserBundle:Default:index.html.twig', array());
    }


}