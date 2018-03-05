<?php

namespace Appstore\Bundle\MedicineBundle\Controller;

use Appstore\Bundle\MedicineBundle\Entity\DentalInvestigation;
use Appstore\Bundle\MedicineBundle\Form\DentalInvestigationType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * DentalInvestigationController controller.
 *
 */
class DentalInvestigationController extends Controller
{

    public function paginate($entities)
    {
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            50  /*limit per page*/
        );
        return $pagination;
    }


    /**
     * Lists all DentalInvestigation entities.
     *
     */
    public function indexAction()
    {
        $entity = new DentalInvestigation();
        $data = $_REQUEST;
        $em = $this->getDoctrine()->getManager();
        $pagination = $em->getRepository('MedicineBundle:DentalInvestigation')->findAll();
        $editForm = $this->createCreateForm($entity);
        return $this->render('MedicineBundle:DentalInvestigation:index.html.twig', array(
            'pagination' => $pagination,
            'searchForm' => $data,
            'form'   => $editForm->createView(),
        ));

    }

    /**
     * Creates a new DentalInvestigation entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new DentalInvestigation();
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getDmsConfig();
        $pagination = $em->getRepository('MedicineBundle:DentalInvestigation')->getServiceLists($config);
        //$pagination = $this->paginate($pagination);
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('dms_particular'));
        }

        return $this->render('MedicineBundle:DentalInvestigation:index.html.twig', array(
            'entity' => $entity,
            'pagination' => $pagination,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a DentalInvestigation entity.
     *
     * @param DentalInvestigation $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(DentalInvestigation $entity)
    {
        $form = $this->createForm(new DentalInvestigationType(), $entity, array(
            'action' => $this->generateUrl('dms_particular_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }



    /**
     * Displays a form to edit an existing DentalInvestigation entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getDmsConfig();
        $pagination = $em->getRepository('MedicineBundle:DentalInvestigation')->getServiceLists($config);
        //$pagination = $this->paginate($pagination);
        $entity = $em->getRepository('MedicineBundle:DentalInvestigation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DentalInvestigation entity.');
        }
        $editForm = $this->createEditForm($entity);

        return $this->render('MedicineBundle:DentalInvestigation:index.html.twig', array(
            'entity'      => $entity,
            'pagination'      => $pagination,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a DentalInvestigation entity.
     *
     * @param DentalInvestigation $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(DentalInvestigation $entity)
    {

        $config = $this->getUser()->getGlobalOption()->getDmsConfig();
        $form = $this->createForm(new DentalInvestigationType(), $entity, array(
            'action' => $this->generateUrl('dms_particular_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing DentalInvestigation entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getDmsConfig();
        $pagination = $em->getRepository('MedicineBundle:DentalInvestigation')->getServiceLists($config);
        //$pagination = $this->paginate($pagination);
        $entity = $em->getRepository('MedicineBundle:DentalInvestigation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DentalInvestigation entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('dms_particular'));
        }

        return $this->render('MedicineBundle:DentalInvestigation:index.html.twig', array(
            'entity'      => $entity,
            'pagination'      => $pagination,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a DentalInvestigation entity.
     *
     */
    public function deleteAction(DentalInvestigation $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DentalInvestigation entity.');
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
        }catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'notice', 'Please contact system administrator further notification.'
            );
        }
        return $this->redirect($this->generateUrl('dms_particular'));
    }

   
    /**
     * Status a Page entity.
     *
     */
    public function statusAction(DentalInvestigation $entity)
    {

        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }

        $status = $entity->getStatus();
        if($status == 1){
            $entity->setStatus(0);
        } else{
            $entity->setStatus(1);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('dms_particular'));
    }
}
