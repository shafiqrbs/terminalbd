<?php

namespace Appstore\Bundle\CustomerBundle\Controller;

use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\DomainUserBundle\Form\MemberEditProfileType;
use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\EcommerceBundle\Form\OrderType;
use Frontend\FrontentBundle\Service\Cart;
use Setting\Bundle\ToolBundle\Entity\Module;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentController extends Controller
{


    public function paginate($entities)
    {
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        $pagination->setTemplate('SettingToolBundle:Widget:pagination.html.twig');
        return $pagination;
    }

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $data['type'] = 'member';
        $entities = $em->getRepository('DomainUserBundle:Customer')->findWithSearch($globalOption,$data);
        $pagination = $this->paginate($entities);
        $batches = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->studentBatchChoiceList();
        return $this->render('CustomerBundle:Student:index.html.twig', array(
            'globalOption' => $globalOption,
            'batches' => $batches,
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }

    /**
     * Displays a form to edit an existing DomainUser entity.
     *
     */
    public function editAction()
    {
        $user = $this->getUser();
        $profile = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $user->getGlobalOption(),'user' => $user->getId()));
        $editForm = $this->createEditForm($profile);
        $globalOption = $this->getUser()->getGlobalOption();
        return $this->render('CustomerBundle:Student:new.html.twig', array(
            'globalOption' => $globalOption,
            'entity'      => $profile,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a DomainUser entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Customer $profile)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new MemberEditProfileType($globalOption,$location), $profile, array(
            'action' => $this->generateUrl('customerweb_profile_update', array('shop' => $globalOption->getSlug())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing DomainUser entity.
     *
     */
    public function updateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $profile = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $user->getGlobalOption(),'user' => $user->getId()));
        if (!$profile) {
            throw $this->createNotFoundException('Unable to find customer entity.');
        }
        $editForm = $this->createEditForm($profile);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $profile->upload();
            $em->flush();
            return $this->redirect($this->generateUrl('customerweb_profile', array('shop' => $user->getGlobalOption()->getSlug())));
        }
        return $this->render('CustomerBundle:Student:new.html.twig', array(
            'globalOption'      =>  $user->getGlobalOption(),
            'entity'            => $profile,
            'form'              => $editForm->createView(),

        ));
    }


}
