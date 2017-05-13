<?php

namespace Appstore\Bundle\CustomerBundle\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\EcommerceBundle\Form\OrderType;
use Core\UserBundle\Entity\User;
use Core\UserBundle\Form\SignupType;
use Frontend\FrontentBundle\Service\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AgentClientController extends Controller
{


    public function paginate($entities)
    {

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        return $pagination;
    }

    /**
     * Lists all GlobalOption entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $entities = $em->getRepository('SettingToolBundle:GlobalOption')->findBy(array('agent' => $user));
        $entities = $this->paginate($entities);
        return $this->render('CustomerBundle:Agent:index.html.twig', array(
            'entities' => $entities,
            'user' => $user,
            'globalOption' => '',
        ));
    }
    /**
     * Creates a new GlobalOption entity.
     *
     */
    public function createAction(Request $request)
    {

        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $intlMobile = $entity->getProfile()->getMobile();
        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($intlMobile);
        $entity->getProfile()->setMobile($mobile);
        $data = $request->request->all();
        if ($form->isValid()) {
            $user = $this->getUser();
            $globalOption = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->createGlobalOption($mobile,$data,$user);
            $entity->setPlainPassword("1234");
            $entity->setGlobalOption($globalOption);
            $entity->setEnabled(true);
            $entity->setDomainOwner(true);
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
            return $this->redirect($this->generateUrl('agentclient'));
        }

        return $this->render('CustomerBundle:Agent:signup.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));

    }

    private function createCreateForm(User $entity)
    {
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $em = $this->getDoctrine()->getRepository('SettingToolBundle:Syndicate');
        $form = $this->createForm(new SignupType($em,$location), $entity, array(
            'action' => $this->generateUrl('agentclient_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'id' => 'signup',
                'class' => 'registration signupForm',
                'novalidate' => 'novalidate',
            )
        ));

        return $form;
    }


    /**
     * Displays a form to create a new GlobalOption entity.
     *
     */
    public function newAction()
    {
        $user = $this->getUser();
        $entity = new User();
        $form   = $this->createCreateForm($entity);
        return $this->render('CustomerBundle:Agent:signup.html.twig', array(
            'entity' => $entity,
            'user' => $user,
            'globalOption' => '',
            'form'   => $form->createView(),
        ));
    }


}
