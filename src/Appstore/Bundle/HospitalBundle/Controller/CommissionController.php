<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Appstore\Bundle\HospitalBundle\Entity\HmsCommission;
use Appstore\Bundle\HospitalBundle\Form\CommissionType;
use Appstore\Bundle\HospitalBundle\Form\VendorType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Commission controller.
 *
 */
class CommissionController extends Controller
{

    /**
     * Lists all Vendor entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entity = new HmsCommission();
        $form   = $this->createCreateForm($entity);
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entities = $this->getDoctrine()->getRepository('HospitalBundle:HmsCommission')->findBy(array('hospitalConfig' => $hospital),array('name'=>'ASC'));
        return $this->render('HospitalBundle:Commission:index.html.twig', array(
            'entities' => $entities,
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    /**
     * Creates a new Vendor entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new HmsCommission();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
            $entity->setHospitalConfig($hospital);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('hms_commission', array('id' => $entity->getId())));
        }

        return $this->render('HospitalBundle:Commission:index.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Vendor entity.
     *
     * @param Vendor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(HmsCommission $entity)
    {
        $form = $this->createForm(new CommissionType(), $entity, array(
            'action' => $this->generateUrl('hms_commission_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to edit an existing Vendor entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('HospitalBundle:HmsCommission')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Commission entity.');
        }

        $editForm = $this->createEditForm($entity);


        return $this->render('HospitalBundle:Commission:index.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Vendor entity.
    *
    * @param Vendor $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(HmsCommission $entity)
    {
        $form = $this->createForm(new CommissionType(), $entity, array(
            'action' => $this->generateUrl('hms_commission_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing Vendor entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('HospitalBundle:HmsCommission')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been changed successfully"
            );
            return $this->redirect($this->generateUrl('hms_commission_edit', array('id' => $id)));
        }

        return $this->render('HospitalBundle:Commission:index.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a Vendor entity.
     *
     */
    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('HospitalBundle:HmsCommission')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('hms_commission'));
    }


    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('HospitalBundle:HmsCommission')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find commission entity.');
        }

        $status = $entity->isStatus();
        if($status == 1){
            $entity->setStatus(false);
        } else{
            $entity->setStatus(true);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('hms_commission'));
    }



}
