<?php

namespace Core\UserBundle\Controller;

use Doctrine\Common\Util\Debug;
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
            $enable=0;
        }

        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            return $this->redirect($this->generateUrl('admin'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_DOMAIN') && $enable == 1) {
            return $this->redirect($this->generateUrl('domain'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_DOMAIN') && $enable != 1) {
            return $this->redirect($this->generateUrl('bindu_build'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_DOMAIN_INVENTORY') && $enable == 1 ) {
            return $this->redirect($this->generateUrl('domain'));
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

        $user = $this->getUser();
        $em = $this->get('doctrine.orm.entity_manager');
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $todaySalesOverview = $em->getRepository('InventoryBundle:Sales')->todaySalesOverview($inventory);
        return $this->render('UserBundle:Default:domain.html.twig', array(
            'user' => $user,
            'todaySalesOverview' => $todaySalesOverview
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
        exit;
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