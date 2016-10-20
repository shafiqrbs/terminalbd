<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * ApplicationPricing controller.
 *
 */
class DomainController extends Controller
{

    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            20  /*limit per page*/
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
        $entities = $em->getRepository('SettingToolBundle:GlobalOption')->getList();
        $entities = $this->paginate($entities);
        return $this->render('SettingToolBundle:Domain:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    public function optionStatusAction()
    {
        $items = array();
        $items[]=array('value' =>1,'text'=> 'Active');
        $items[]=array('value' =>2,'text'=> 'Hold');
        $items[]=array('value' =>3,'text'=> 'Suspended');
        return new JsonResponse($items);


    }

    public function optionStatusUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingToolBundle:GlobalOption')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }

        $entity->setStatus($data['value']);
        $em->flush();
        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch('setting_tool.post.domain_notification', new \Setting\Bundle\ToolBundle\Event\DomainNotification($entity));

        exit;
    }


}
