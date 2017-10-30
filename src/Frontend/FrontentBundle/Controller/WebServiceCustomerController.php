<?php

namespace Frontend\FrontentBundle\Controller;

use Core\UserBundle\Entity\User;
use Core\UserBundle\Form\CustomerRegisterType;
use Core\UserBundle\Form\SignupType;
use Frontend\FrontentBundle\Service\MobileDetect;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class WebServiceCustomerController extends Controller
{


   public function registerAction($subdomain)
    {

        $entity = new User();
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        $form   = $this->createCreateForm($subdomain,$entity);
        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if( $detect->isMobile() && $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }
            return $this->render('FrontendBundle:'.$theme.':register.html.twig',
                array(
                    'globalOption'  => $globalOption,
                    'pageName'      => 'register',
                    'form'   => $form->createView(),
                 )
            );
        }
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */

    private function createCreateForm($subdomain,User $entity)
    {
        $form = $this->createForm(new CustomerRegisterType(), $entity, array(
            'action' => $this->generateUrl($subdomain.'_webservice_customer_create'),
            'method' => 'POST',
            'attr' => array(
                'id' => 'signup',
                'class' => 'register',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Creates a new User entity.
     *
     */

    public function userCheckingAction(Request $request)
    {
        $intlMobile = $request->query->get('Core_userbundle_user[profile][mobile]',NULL,true);
        $em = $this->getDoctrine()->getManager();
        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($intlMobile);
        $entity = $em->getRepository('UserBundle:User')->findBy(array('username'=> $mobile));

        if( count($entity) > 0 ){
            $valid = 'false';
        }else{
            $valid = 'true';
        }
        echo $valid;
        exit;
    }

    /**
     * Creates a new User entity.
     *
     */

    public function createAction($subdomain, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new User();
        $form = $this->createCreateForm($subdomain,$entity);
        $form->handleRequest($request);
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        if ($form->isValid()) {

            $intlMobile = $entity->getProfile()->getMobile();
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($intlMobile);
            
            $entity->setPlainPassword("1234");
            $entity->setEnabled(true);
            $entity->setUsername($mobile);
            if(empty($entity->getEmail())){
                $entity->setEmail($mobile.'@gmail.com');
            }
            $entity->setRoles(array('ROLE_CUSTOMER'));
            $em->persist($entity);
            $em->flush();

            //$dispatcher = $this->container->get('event_dispatcher');
            //$dispatcher->dispatch('setting_tool.post.user_signup_msg', new \Setting\Bundle\ToolBundle\Event\UserSignup($entity));
            return $this->redirect($this->generateUrl('webservice_customer_confirm',array('subdomain' => $subdomain)));
        }
        if(!empty($globalOption)){

            $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
            /* Device Detection code desktop or mobile */

            $detect = new MobileDetect();
            if( $detect->isMobile() &&  $detect->isTablet() ) {
                $theme = 'Template/Mobile/'.$themeName;
            }else{
                $theme = 'Template/Desktop/'.$themeName;
            }
            return $this->render('FrontendBundle:'.$theme.':register.html.twig',
                array(
                    'globalOption'  => $globalOption,
                    'pageName'      => 'register',
                    'form'   => $form->createView(),
                )
            );
        }

    }

    public function insertAction($subdomain, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new User();
        $form = $this->createCreateForm($subdomain,$entity);
        $form->handleRequest($request);
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        $intlMobile = $entity->getProfile()->getMobile();
        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($intlMobile);
        $entity->getProfile()->setMobile($mobile);
        if ($form->isValid()) {
            $entity->setPlainPassword("1234");
            $entity->setEnabled(true);
            $entity->setUsername($mobile);
            if(empty($entity->getEmail())){
                $entity->setEmail($mobile.'@gmail.com');
            }
            $entity->setRoles(array('ROLE_CUSTOMER'));
            $em->persist($entity);
            $em->flush();
            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('setting_tool.post.customer_signup_msg', new \Setting\Bundle\ToolBundle\Event\CustomerSignup($entity,$globalOption));
            return new Response('success');

        }else{

            return new Response('invalid');
        }

        exit;


    }


    public function confirmAction($subdomain)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new User();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        if ($this->has('security.csrf.token_manager')) {
            $csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();
        } else {
            // BC for SF < 2.4
            $csrfToken = $this->has('form.csrf_provider')
                ? $this->get('form.csrf_provider')->generateCsrfToken('authenticate')
                : null;
        }
        $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
        $detect = new MobileDetect();
        if( $detect->isMobile() &&  $detect->isTablet() ) {
            $theme = 'Template/Mobile/'.$themeName;
        }else{
            $theme = 'Template/Desktop/'.$themeName;
        }
        return $this->render('FrontendBundle:'.$theme.':login.html.twig',
            array(
                'globalOption'  => $globalOption,
                'entity' => $entity,
                'error' => '',
                'pageName'      => 'login',
                'csrf_token' => $csrfToken,
            )
        );
    }

    public function domainLoginAction($subdomain)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new User();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        if ($this->has('security.csrf.token_manager')) {
            $csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();
        } else {
            // BC for SF < 2.4
            $csrfToken = $this->has('form.csrf_provider')
                ? $this->get('form.csrf_provider')->generateCsrfToken('authenticate')
                : null;
        }
        $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
        $detect = new MobileDetect();
        if( $detect->isMobile() &&  $detect->isTablet() ) {
            $theme = 'Template/Mobile/'.$themeName;
        }else{
            $theme = 'Template/Desktop/'.$themeName;
        }
        return $this->render('FrontendBundle:'.$theme.':login.html.twig',
            array(
                'globalOption'  => $globalOption,
                'entity' => $entity,
                'error' => '',
                'csrf_token' => $csrfToken,
            )
        );
    }

    public function domainCustomerHomeAction($subdomain)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        $themeName = $globalOption->getSiteSetting()->getTheme()->getFolderName();
        /* Device Detection code desktop or mobile */

        $detect = new MobileDetect();
        if( ! $detect->isMobile() && ! $detect->isTablet() ) {
            $theme = 'Template/Mobile/'.$themeName;
        }else{
            $theme = 'Template/Desktop/'.$themeName;
        }
        return $this->render('FrontendBundle:'.$theme.':dashboard.html.twig',
            array(
                'globalOption'  => $globalOption,
            )
        );
    }

    public function customerForgetPasswordAction()
    {

        $mobile =  $_REQUEST['mobile'];
        $user = $this->getDoctrine()->getRepository('UserBundle:User')->findOneBy(array('username' => $mobile));
        if($user){
            $a = mt_rand(1000,9999);
            $user->setPlainPassword($a);
            $this->get('fos_user.user_manager')->updateUser($user);
            $this->get('session')->getFlashBag()->add(
                'success',"Password reset successfully"
            );
            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('setting_tool.post.change_password', new \Setting\Bundle\ToolBundle\Event\PasswordChangeSmsEvent($user,'123456'));
            echo 'Your reset password is '.$a;
        }else{
            echo 'This mobile '.$mobile.' is not correct,Please try another mobile no.';
        }
        exit;
    }



}
