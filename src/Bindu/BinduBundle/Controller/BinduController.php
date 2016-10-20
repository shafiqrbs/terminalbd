<?php

namespace Bindu\BinduBundle\Controller;

use Core\UserBundle\Entity\User;
use Core\UserBundle\Form\SignupType;
use Core\UserBundle\Form\UserConfirmType;
use Frontend\FrontentBundle\Service\MobileDetect;
use Setting\Bundle\ToolBundle\Event\ReceiveSmsEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BinduController extends Controller
{



    public function indexAction()
    {

        $slides = $this->getDoctrine()->getRepository('SettingContentBundle:SiteSlider')->findBy(array(),array('id'=>'DESC'));
        $entity = new User();
        $form   = $this->createCreateForm($entity);
        $detect = new MobileDetect();
        if( $detect->isMobile() OR  $detect->isTablet() ) {
            $theme = 'Frontend/Mobile';
        }else{
            $theme = 'Frontend/Desktop';
        }
        return $this->render('BinduBundle:'.$theme.':index.html.twig', array(
            'entity' => $entity,
            'slides' => $slides,
            'form'   => $form->createView(),
        ));

    }

    public function builderAction()
    {

        $entity = new User();
        $form   = $this->createCreateForm($entity);
        $detect = new MobileDetect();
        if($detect->isMobile() OR $detect->isTablet() ) {
            $theme = 'Frontend/Mobile';
        }else{
            $theme = 'Frontend/Desktop';
        }
        return $this->render('BinduBundle:'.$theme.':webBuilder.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));

    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */

    private function createCreateForm(User $entity)
    {
        $em = $this->getDoctrine()->getRepository('SettingToolBundle:Syndicate');
        $form = $this->createForm(new SignupType($em), $entity, array(
            'action' => $this->generateUrl('bindu_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'id' => 'signup',
                'class' => 'signupForm',
                'novalidate' => 'novalidate',
            )
        ));

        return $form;
    }


    /**
     * Creates a new User entity.
     *
     */

    public function createAction(Request $request)
    {

        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $intlMobile = $entity->getProfile()->getMobile();
        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($intlMobile);

        $data = $request->request->all();


        if ($form->isValid()) {

            $globalOption = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->createGlobalOption($mobile,$data);
            $entity->setPlainPassword("1234");
            $entity->setGlobalOption($globalOption);
            $entity->setEnabled(true);
            $entity->setUsername($mobile);
            $entity->setEmail($mobile.'@gmail.com');
            $entity->setRoles(array('ROLE_DOMAIN'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->initialSetup($entity);
            $this->get('settong.toolManageRepo')->createDirectory($entity->getGlobalOption()->getId());

            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('setting_tool.post.user_signup_msg', new \Setting\Bundle\ToolBundle\Event\UserSignup($entity));
            return $this->redirect($this->generateUrl('bindu_confirm'));
        }
        $detect = new MobileDetect();
        if( $detect->isMobile() OR  $detect->isTablet()){

            $theme = 'Frontend/Mobile';
            return $this->render('BinduBundle:'.$theme.':webBuilder.html.twig', array(
                'entity' => $entity,
                'form'   => $form->createView(),

            ));
        }else{
            $theme = 'Frontend/Desktop';
            return $this->render('BinduBundle:'.$theme.':index.html.twig', array(
                'entity' => $entity,
                'form'   => $form->createView(),

            ));
        }

    }

    public function userCheckingAction(Request $request)
    {

        $intlMobile = $request->query->get('Core_userbundle_user[profile][mobile]',NULL,true);
        $em = $this->getDoctrine()->getManager();
        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($intlMobile);
        $entity = $em->getRepository('UserBundle:User')->findBy(array('username'=>$mobile));

        if( count($entity) > 0 ){
            return new Response('failed');

        }else{
            return new Response('success');
        }
        exit;
    }

    public function checkUserNameAction(Request $request)
    {

        $mobile = $request->request->get('mobile');
        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($mobile);

        $username = $this->getDoctrine()->getRepository('UserBundle:User')->findOneBy(array('username' => $mobile));
        $profileMobile = $this->getDoctrine()->getRepository('UserBundle:Profile')->findOneBy(array('mobile'=>$mobile));
        $optionMobile = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('mobile'=>$mobile));

        if(empty($username) && empty($profileMobile) && empty($optionMobile)){
            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;
    }



    public function confirmAction()
    {

        $entity = new User();

        if ($this->has('security.csrf.token_manager')) {
            $csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();
        } else {
            // BC for SF < 2.4
            $csrfToken = $this->has('form.csrf_provider')
                ? $this->get('form.csrf_provider')->generateCsrfToken('authenticate')
                : null;
        }

        $detect = new MobileDetect();
        if($detect->isMobile() && $detect->isTablet() ) {
            $theme = 'Frontend/Mobile';
        }else{
            $theme = 'Frontend/Desktop';
        }
        return $this->render('BinduBundle:'.$theme.':confirm.html.twig', array(
            'entity' => $entity,
            'csrf_token' => $csrfToken,
        ));


    }

    /**
     * Creates a new User entity.
     *
     */

    public function checkAction(Request $request)
    {

        $entity = new User();
        $form = $this->createLoginForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            return $this->redirect($this->generateUrl('bindu_build'));
        }
        return $this->render('BinduBundle:Bindu:confirm.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),

        ));
    }

    public function previewAction()
    {
        $user = $this->getUser();
        $entity = $user->getGlobalOption();
        return $this->render('BinduBundle:Bindu:preview.html.twig', array(
            'entity' => $entity,
        ));

    }

    public function findAction()
    {
        $entities =$this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findBy(array('status'=>1),array('id'=>'desc'));
        /* Device Detection code desktop or mobile */
        $detect = new MobileDetect();
        if( $detect->isMobile() OR  $detect->isTablet() ) {
            $theme = 'Frontend/Mobile';
        }else{
            $theme = 'Frontend/Desktop';
        }
        return $this->render('BinduBundle:'.$theme.':find.html.twig', array(
            'entities' => $entities,
        ));

    }


    public function partnerAction()
    {
        $entities =$this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findBy(array('status'=>1));
        return $this->render('BinduBundle:Bindu:find.html.twig', array(
            'entities' => $entities,
        ));

    }

    public function businessDirectoryAction()
    {
        $detect = new MobileDetect();
        if( $detect->isMobile() OR  $detect->isTablet() ) {
            $theme = 'Frontend/Mobile';
        }else{
            $theme = 'Frontend/Desktop';
        }
        return $this->render('BinduBundle:'.$theme.':business.html.twig', array(
            'directory' => '',
        ));


    }

    public function businessDirectoryDetailsAction($directory)
    {

        $syndicate = $this->getDoctrine()->getRepository('SettingToolBundle:Syndicate')->findOneBy(array('slug'=>$directory));
        $entities =$this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findBy(array('status'=>1,'syndicate'=>$syndicate),array('name'=>'ASC'));
        $detect = new MobileDetect();
        if( $detect->isMobile() OR  $detect->isTablet() ) {
            $theme = 'Frontend/Mobile';
        }else{
            $theme = 'Frontend/Desktop';
        }
        return $this->render('BinduBundle:'.$theme.':find.html.twig', array(
            'entities' => $entities,
        ));

    }

    public function locationDirectoryAction()
    {
        $detect = new MobileDetect();
        if( $detect->isMobile() OR  $detect->isTablet() ) {
            $theme = 'Frontend/Mobile';
        }else{
            $theme = 'Frontend/Desktop';
        }
        return $this->render('BinduBundle:'.$theme.':location.html.twig');

    }

    public function locationDirectoryDetailsAction($location)
    {

        $entities =$this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findBy(array('status'=>1,'location'=>$location),array('name'=>'ASC'));
        $detect = new MobileDetect();
        if( $detect->isMobile() OR  $detect->isTablet() ) {
            $theme = 'Frontend/Mobile';
        }else{
            $theme = 'Frontend/Desktop';
        }
        return $this->render('BinduBundle:'.$theme.':find.html.twig', array(
        'entities' => $entities,
        ));

    }

    public function businessLocationAction($business,$location)
    {
        $syndicate = $this->getDoctrine()->getRepository('SettingToolBundle:Syndicate')->findOneBy(array('slug'=>$business));
        $entities =$this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findBy(array('status'=>1,'location'=>$location,'syndicate'=>$syndicate),array('name'=>'ASC'));
        $detect = new MobileDetect();
        if( $detect->isMobile() OR  $detect->isTablet() ) {
            $theme = 'Frontend/Mobile';
        }else{
            $theme = 'Frontend/Desktop';
        }
        return $this->render('BinduBundle:'.$theme.':find.html.twig', array(
        'entities' => $entities,
        ));

    }

    public function pageContentAction($slug)
    {
        $entity =$this->getDoctrine()->getRepository('SettingContentBundle:SiteContent')->findOneBy(array('slug'=>$slug));
        $detect = new MobileDetect();
        if( $detect->isMobile() OR  $detect->isTablet() ) {
            $theme = 'Frontend/Mobile';
        }else{
            $theme = 'Frontend/Desktop';
        }
        return $this->render('BinduBundle:'.$theme.':content.html.twig', array(
            'entity' => $entity,
        ));
    }

    public function contactAction()
    {
        $detect = new MobileDetect();
        if( $detect->isMobile() OR  $detect->isTablet() ) {
            $theme = 'Frontend/Mobile';
        }else{
            $theme = 'Frontend/Desktop';
        }
        return $this->render('BinduBundle:'.$theme.':contact.html.twig');
    }

    public function aboutAction()
    {
        $entity =$this->getDoctrine()->getRepository('SettingContentBundle:SiteContent')->findOneBy(array('slug'=>'about-us'));
        $detect = new MobileDetect();
        if( $detect->isMobile() OR  $detect->isTablet() ) {
            $theme = 'Frontend/Mobile';
        }else{
            $theme = 'Frontend/Desktop';
        }
        return $this->render('BinduBundle:'.$theme.':about.html.twig', array(
            'entity' => $entity,
        ));
    }


    public function serviceAction()
    {
        $detect = new MobileDetect();
        if( $detect->isMobile() &&  $detect->isTablet() ) {
            $theme = 'Frontend/Mobile';
        }else{
            $theme = 'Frontend/Desktop';
        }
        return $this->render('BinduBundle:'.$theme.':product.html.twig');

    }

    public function receiveSMSAction(Request $request)
    {
        $data = $request->request->all();
        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['mobile']);
        $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->insertContactCustomer($data,$mobile);
        $customerInbox = $this->getDoctrine()->getRepository('DomainUserBundle:CustomerInbox')->sendCustomerMessage($customer,$data,'sms');
        if( $customer->getGlobalOption()->isEmailIntegration() == 1 AND $customer->getGlobalOption()->getEmail() !="" )
        {
            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('setting_tool.post.email_receive', new ReceiveSmsEvent($customer->getGlobalOption(),$customerInbox));

        }
        return new Response('success');
    }

    public function receiveEmailAction(Request $request)
    {
        $data = $request->request->all();
        $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->insertContactCustomer($data);
        $customerInbox = $this->getDoctrine()->getRepository('DomainUserBundle:CustomerInbox')->sendCustomerMessage($customer,$data,'email');

        if( $customer->getGlobalOption()->isSmsIntegration() == 1 AND $customer->getGlobalOption()->getMobile() !="" ) {
            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('setting_tool.post.sms_receive', new ReceiveSmsEvent($customer->getGlobalOption(), $customerInbox));

        }
        return new Response('success');


    }



}
