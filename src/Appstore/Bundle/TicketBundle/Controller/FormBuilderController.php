<?php

namespace Appstore\Bundle\TicketBundle\Controller;

use Appstore\Bundle\TicketBundle\Entity\TicketFormBuilder;
use Appstore\Bundle\TicketBundle\Form\FormBuilderType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * TicketFormBuilder controller.
 *
 */
class FormBuilderController extends Controller
{


    /**
     * @Secure(roles="ROLE_TICKET_MANAGER")
     */


    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getTicketConfig();
        $entities = $em->getRepository('TicketBundle:TicketFormBuilder')->findBy(array('config'=>$config));
        return $this->render('TicketBundle:FormBuilder:index.html.twig', array(
            'entities' => $entities,
            'searchForm' => $data,
           // 'overview' => $overview,
        ));
    }

    /**
     * Creates a new TicketFormBuilder entity.
     * @Secure(roles="ROLE_TICKET_MANAGER")
     */

    public function createAction(Request $request)
    {
        $entity = new TicketFormBuilder();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $data = $request->request->all();
        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $entity->setConfig($this->getUser()->getGlobalOption()->getTicketConfig());
            $name = $entity->getModule()->getName();
            $entity->setName($name);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            $this->getDoctrine()->getRepository('TicketBundle:TicketBuilderItem')->pageMeta($entity,$data);
            return $this->redirect($this->generateUrl('formbuilder'));
        }

        return $this->render('TicketBundle:FormBuilder:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a TicketFormBuilder entity.
     *
     * @param TicketFormBuilder $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TicketFormBuilder $entity)
    {
        $config = $this->getUser()->getGlobalOption()->getTicketConfig();
        $form = $this->createForm(new FormBuilderType($config), $entity, array(
            'action' => $this->generateUrl('formbuilder_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
      return $form;
    }

    /**
     * Displays a form to create a new TicketFormBuilder entity.
     * @Secure(roles="ROLE_TICKET_MANAGER")
     */

    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new TicketFormBuilder();
        $form   = $this->createCreateForm($entity);
        return $this->render('TicketBundle:FormBuilder:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a TicketFormBuilder entity.
     * @Secure(roles="ROLE_TICKET_MANAGER")
     */

    public function showAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TicketBundle:TicketFormBuilder')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TicketFormBuilder entity.');
        }
        return $this->render('TicketBundle:FormBuilder:show.html.twig', array(
            'entity'      => $entity,
        ));

    }

    /**
     * Displays a form to edit an existing TicketFormBuilder entity.
     * @Secure(roles="ROLE_TICKET_MANAGER")
     */

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TicketBundle:TicketFormBuilder')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TicketFormBuilder entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('TicketBundle:FormBuilder:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }


    /**
    * Creates a form to edit a TicketFormBuilder entity.
    *
    * @param TicketFormBuilder $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TicketFormBuilder $entity)
    {
        $config = $this->getUser()->getGlobalOption()->getTicketConfig();
        $form = $this->createForm(new FormBuilderType($config), $entity, array(
            'action' => $this->generateUrl('formbuilder_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Edits an existing TicketFormBuilder entity.
     * @Secure(roles="ROLE_TICKET_MANAGER")
     */

    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $entity = $em->getRepository('TicketBundle:TicketFormBuilder')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TicketFormBuilder entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $name = $entity->getModule()->getName();
            $entity->setName($name);
            $em->flush();
            $this->getDoctrine()->getRepository('TicketBundle:TicketBuilderItem')->pageMeta($entity,$data);
            return $this->redirect($this->generateUrl('formbuilder_edit',array('id'=>$entity->getId())));
        }
        return $this->render('TicketBundle:FormBuilder:new.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Expenditure entity.
     * @Secure(roles="ROLE_TICKET_MANAGER")
     */

    public function deleteAction(TicketFormBuilder $entity)
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
        return $this->redirect($this->generateUrl('formbuilder'));
    }

}
