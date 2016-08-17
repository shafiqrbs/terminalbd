<?php

namespace Syndicate\Bundle\ComponentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Syndicate\Bundle\ComponentBundle\Entity\Scholarship;
use Syndicate\Bundle\ComponentBundle\Form\ScholarshipType;

/**
 * Scholarship controller.
 *
 */
class ScholarshipController extends Controller
{

    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            30  /*limit per page*/
        );
        return $pagination;
    }

    /**
     * Lists all Scholarship entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SyndicateComponentBundle:Scholarship')->findAll();
        $pagination = $this->paginate($entities);
        return $this->render('SyndicateComponentBundle:Scholarship:index.html.twig', array(
            'pagination' => $pagination,
        ));
    }
    /**
     * Creates a new Scholarship entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Scholarship();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $entity->upload();
            $em->persist($entity);
            $em->flush();

            $syndicates= $this->get('request')->request->get('subSyndicates');
            $this->getDoctrine()->getRepository('SyndicateComponentBundle:Scholarship')->setScholarSyndicate($entity,$syndicates);
            return $this->redirect($this->generateUrl('scholarship_show', array('id' => $entity->getId())));
        }
        $subSyndicates = $this->getSubSyndicateUnderScholar($entity);
        return $this->render('SyndicateComponentBundle:Scholarship:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'subSyndicates' => $subSyndicates
        ));
    }

    /**
     * Creates a form to create a Scholarship entity.
     *
     * @param Scholarship $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Scholarship $entity)
    {
        $em = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new ScholarshipType($em), $entity, array(
            'action' => $this->generateUrl('scholarship_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new Scholarship entity.
     *
     */
    public function newAction()
    {
        $entity = new Scholarship();
        $form   = $this->createCreateForm($entity);
        $subSyndicates = $this->getSubSyndicateUnderScholar($entity);
        return $this->render('SyndicateComponentBundle:Scholarship:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'subSyndicates' => $subSyndicates
        ));
    }

    /**
     * Finds and displays a Scholarship entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SyndicateComponentBundle:Scholarship')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Scholarship entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SyndicateComponentBundle:Scholarship:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Scholarship entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SyndicateComponentBundle:Scholarship')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Scholarship entity.');
        }

        $editForm = $this->createEditForm($entity);
        $subSyndicates = $this->getSubSyndicateUnderScholar($entity);
        return $this->render('SyndicateComponentBundle:Scholarship:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'subSyndicates' => $subSyndicates

        ));
    }

    /**
    * Creates a form to edit a Scholarship entity.
    *
    * @param Scholarship $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Scholarship $entity)
    {

        $em = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new ScholarshipType($em), $entity, array(
            'action' => $this->generateUrl('scholarship_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing Scholarship entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SyndicateComponentBundle:Scholarship')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Scholarship entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {


            $entity->upload();
            $em->flush();
            $syndicates= $this->get('request')->request->get('subSyndicates');
            $this->getDoctrine()->getRepository('SyndicateComponentBundle:Scholarship')->setScholarSyndicate($entity,$syndicates);

            return $this->redirect($this->generateUrl('scholarship_edit', array('id' => $id)));
        }
        $subSyndicates = $this->getSubSyndicateUnderScholar($entity);

        return $this->render('SyndicateComponentBundle:Scholarship:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'subSyndicates' => $subSyndicates
        ));
    }
    /**
     * Deletes a Scholarship entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SyndicateComponentBundle:Scholarship')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Scholarship entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('scholarship'));
    }

    /**
     * Creates a form to delete a Scholarship entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('scholarship_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    public function getSubSyndicateUnderScholar($entity)
    {
        $em = $this->getDoctrine()->getManager();
        $parentId       = 1 ;
        $syndicates     = $em->getRepository('SettingToolBundle:Syndicate')->findBy(array('status'=>1,'parent'=>$parentId),array('name'=>'asc'));
        return $subSyndicates  = $this->getDoctrine()->getRepository('SettingToolBundle:Syndicate')->getSyndicateUnderScholars($syndicates,$entity);

    }

}
