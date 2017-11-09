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

        if( $user->getGlobalOption()){
            $enable = $user->getGlobalOption()->getStatus();
        }else{
            $enable = 0;
        }


        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_DOMAIN') && $enable == 1) {
            return $this->redirect($this->generateUrl('domain'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_WEBSITE') && $enable == 1) {
            return $this->redirect($this->generateUrl('website'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_DOMAIN') && $enable != 1) {
            return $this->redirect($this->generateUrl('bindu_build'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_INVENTORY') && $enable == 1 ) {
            return $this->redirect($this->generateUrl('inventoryconfig_dashboard'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_HOSPITAL') && $enable == 1 ) {
            return $this->redirect($this->generateUrl('hospital_homepage'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_DOMAIN_ACCOUNTING') && $enable == 1 ) {
            return $this->redirect($this->generateUrl('domain'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_DOMAIN_INVENTORY_SALES') && $enable == 1) {

            $inventory = $user->getGlobalOption()->getInventoryConfig();
            $deliveryProcess = $inventory->getDeliveryProcess();
            if (!empty($deliveryProcess)) {

                if (in_array('Pos', $deliveryProcess)) {
                    return $this->redirect($this->generateUrl('inventory_sales'));
                } elseif (in_array('OnlineSales', $deliveryProcess)) {
                    return $this->redirect($this->generateUrl('inventory_salesonline'));
                } elseif (in_array('GeneralSales', $deliveryProcess)) {
                    return $this->redirect($this->generateUrl('inventory_salesgeneral'));
                } elseif (in_array('ManualSales', $deliveryProcess)) {
                    return $this->redirect($this->generateUrl('inventory_salesmanual'));
                } elseif (in_array('Order', $deliveryProcess)) {
                    return $this->redirect($this->generateUrl('inventory_customer'));
                }
            }

        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_DOMAIN_INVENTORY_PURCHASE') && $enable == 1) {
            return $this->redirect($this->generateUrl('purchase'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_AGENT')) {
              return $this->redirect($this->generateUrl('agentclient'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_CUSTOMER')) {
              return $this->redirect($this->generateUrl('customer'));
        }elseif (!empty($user) && $enable == 2 ) {
            return $this->redirect($this->generateUrl('domain_pendig'));
        }elseif (!empty($user) && $enable == 3 ) {
            return $this->redirect($this->generateUrl('domain_suspended'));
        }else{
            return $this->redirect($this->generateUrl('fos_user_security_login'));
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
        $user = $this->getUser();
        return $this->render('UserBundle:Default:website.html.twig', array(
            'user' => $user,
        ));
    }

    public function hospitalAction()
    {
        $user = $this->getUser();
        return $this->render('UserBundle:Default:website.html.twig', array(
            'user' => $user,
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