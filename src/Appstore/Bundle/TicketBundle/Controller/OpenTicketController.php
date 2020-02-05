<?php

namespace Appstore\Bundle\TicketBundle\Controller;

use Appstore\Bundle\TicketBundle\Entity\Ticket;
use Appstore\Bundle\TicketBundle\Form\openticketType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ticket controller.
 *
 */
class OpenTicketController extends Controller
{


    /**
     * @Secure(roles="ROLE_TICKET")
     */


    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getTicketConfig();
        $entities = $em->getRepository('TicketBundle:Ticket')->findBy(array('config'=>$config));
        return $this->render('TicketBundle:OpenTicket:index.html.twig', array(
            'entities' => $entities,
            'searchForm' => $data,
           // 'overview' => $overview,
        ));
    }

    /**
     * Creates a new Ticket entity.
     * @Secure(roles="ROLE_TICKET")
     */

    public function createAction(Request $request)
    {
        $entity = new Ticket();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $data = $request->request->all();
        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $entity->setConfig($this->getUser()->getGlobalOption()->getTicketConfig());
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            $this->getDoctrine()->getRepository('TicketBundle:TicketBuilderItem')->pageMeta($entity,$data);
            return $this->redirect($this->generateUrl('openticket'));
        }

        return $this->render('TicketBundle:OpenTicket:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Ticket entity.
     *
     * @param Ticket $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Ticket $entity)
    {
        $config = $this->getUser()->getGlobalOption()->getTicketConfig();
        $form = $this->createForm(new openticketType($config), $entity, array(
            'action' => $this->generateUrl('openticket_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
      return $form;
    }

    /**
     * Displays a form to create a new Ticket entity.
     * @Secure(roles="ROLE_TICKET")
     */

    public function newAction($ticket)
    {
        $entity = $this->getDoctrine()->getRepository('TicketBundle:TicketFormBuilder')->findOneBy(array("slug" => $ticket));
        return $this->render('TicketBundle:OpenTicket:new.html.twig', array(

            'entity' => $entity,
        ));
    }

    /**
     * Finds and displays a Ticket entity.
     * @Secure(roles="ROLE_TICKET")
     */

    public function showAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TicketBundle:Ticket')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ticket entity.');
        }
        return $this->render('TicketBundle:OpenTicket:show.html.twig', array(
            'entity'      => $entity,
        ));

    }

    /**
     * Displays a form to edit an existing Ticket entity.
     * @Secure(roles="ROLE_TICKET")
     */

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TicketBundle:Ticket')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ticket entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('TicketBundle:OpenTicket:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }


    /**
    * Creates a form to edit a Ticket entity.
    *
    * @param Ticket $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Ticket $entity)
    {
        $config = $this->getUser()->getGlobalOption()->getTicketConfig();
        $form = $this->createForm(new openticketType($config), $entity, array(
            'action' => $this->generateUrl('openticket_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Edits an existing Ticket entity.
     * @Secure(roles="ROLE_TICKET")
     */

    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TicketBundle:Ticket')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ticket entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $name = $entity->getModule()->getName();
            $entity->setName($name);
            $em->flush();
            return $this->redirect($this->generateUrl('openticket'));
        }

        return $this->render('TicketBundle:OpenTicket:new.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Expenditure entity.
     * @Secure(roles="ROLE_TICKET")
     */

    public function deleteAction(Ticket $entity)
    {

        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountPurchase entity.');
        }
        try {

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'error',"Data has been deleted successfully"
            );

        } catch (ForeignKeyConstraintViolationException $e) {
            $this->get('session')->getFlashBag()->add(
                'notice',"Data has been relation another Table"
            );
        }
        return $this->redirect($this->generateUrl('openticket'));
    }

}
