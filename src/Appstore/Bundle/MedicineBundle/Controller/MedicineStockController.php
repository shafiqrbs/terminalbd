<?php

namespace Appstore\Bundle\MedicineBundle\Controller;

use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Appstore\Bundle\MedicineBundle\Form\MedicineStockType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * MedicineStockController controller.
 *
 */
class MedicineStockController extends Controller
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

    /**
     * Lists all MedicineStock entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->findWithSearch($config,$data);
        $pagination = $this->paginate($entities);
        $entity = new MedicineStock();
        $form = $this->createCreateForm($entity);
        return $this->render('MedicineBundle:MedicineStock:index.html.twig', array(
            'pagination' => $pagination,
            'entity' => $entity,
            'formShow'            => 'hide',
            'form'   => $form->createView(),
        ));

    }

    public function newAction()
    {
        $entity = new MedicineStock();
        $form = $this->createCreateForm($entity);
        return $this->render('MedicineBundle:MedicineStock:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new MedicineStock entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entity = new MedicineStock();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $medicine = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->find($entity->getName());
            $entity->setMedicineConfig($config);
            $entity->setMedicineBrand($medicine);
            $name = $medicine->getMedicineForm().' '.$medicine->getName().' '.$medicine->getStrength();
            $entity->setName($name);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('medicine_stock'));
        }
        $this->get('session')->getFlashBag()->add(
            'error',"Required field does not input"
        );
        return $this->render('MedicineBundle:MedicineStock:index.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a MedicineStock entity.
     *
     * @param MedicineStock $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MedicineStock $entity)
    {

        $form = $this->createForm(new MedicineStockType(), $entity, array(
            'action' => $this->generateUrl('medicine_stock_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    /**
     * Displays a form to edit an existing MedicineStock entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MedicineBundle:MedicineStock')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicineStock entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render('MedicineBundle:MedicineStock:new.html.twig', array(
            'entity'            => $entity,
            'formShow'            => 'show',
            'form'              => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a MedicineStock entity.
     *
     * @param MedicineStock $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(MedicineStock $entity)
    {
        $form = $this->createForm(new MedicineStockType(), $entity, array(
            'action' => $this->generateUrl('medicine_stock_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing MedicineStock entity.
     *
     */
    public function updateAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MedicineBundle:MedicineStock')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicineStock entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('medicine_stock'));
        }
        return $this->render('MedicineBundle:MedicineStock:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a MedicineStock entity.
     *
     */
    public function deleteAction(MedicineStock $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicineStock entity.');
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
        return $this->redirect($this->generateUrl('medicine_stock'));
    }

   
    /**
     * Status a Page entity.
     *
     */
    public function statusAction(MedicineStock $entity)
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
        return $this->redirect($this->generateUrl('medicine_stock'));
    }

    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MedicineBundle:MedicineStock')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find particular entity.');
        }
        $setField = 'set'.$data['name'];
        $entity->$setField(abs($data['value']));
        $em->flush();
        exit;

    }
}
