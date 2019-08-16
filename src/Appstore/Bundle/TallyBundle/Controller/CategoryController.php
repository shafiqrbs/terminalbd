<?php

namespace Appstore\Bundle\TallyBundle\Controller;


use Appstore\Bundle\TallyBundle\Entity\Category;
use Appstore\Bundle\TallyBundle\Form\TallyCategoryType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * TallyCategory controller.
 *
 */
class CategoryController extends Controller
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
     * @Secure(roles="ROLE_TALLY_SETTING,ROLE_DOMAIN")
     */
    

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('TallyBundle:Category')->findBy(array('globalOption' => $option),array( 'parent' => 'asc' , 'name' => 'asc' ));
        $pagination = $this->paginate($entities);
        return $this->render('TallyBundle:TallyCategory:index.html.twig', array(
            'entities' => $pagination,
        ));

    }

   
    /**
     * Creates a new TallyCategory entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Category();
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createCreateForm($entity,$globalOption);
        $form->handleRequest($request);
        $data = $request->request->all();
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($globalOption);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            $this->getDoctrine()->getRepository('TallyBundle:CategoryMeta')->pageMeta($entity,$data);
            return $this->redirect($this->generateUrl('tallycategory_new', array('id' => $entity->getId())));
        }

        return $this->render('TallyBundle:TallyCategory:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a TallyCategory entity.
     *
     * @param Category $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Category $entity, $globalOption)
    {

        $em = $this->getDoctrine()->getRepository('TallyBundle:Category');
        $emHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead');
        $form = $this->createForm(new TallyCategoryType($em,$emHead,$globalOption), $entity, array(
            'action' => $this->generateUrl('tallycategory_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new TallyCategory entity.
     *
     */
    public function newAction()
    {
        $entity = new Category();
        $globalOption = $this->getUser()->getGlobalOption();
        $form   = $this->createCreateForm($entity,$globalOption);

        return $this->render('TallyBundle:TallyCategory:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a TallyCategory entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TallyBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TallyCategory entity.');
        }
        return $this->render('TallyBundle:TallyCategory:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing TallyCategory entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TallyBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TallyCategory entity.');
        }
        $globalOption = $this->getUser()->getGlobalOption();
        $editForm = $this->createEditForm($entity,$globalOption);

        return $this->render('TallyBundle:TallyCategory:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a TallyCategory entity.
     *
     * @param Category $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Category $entity, $globalOption)
    {
        $em = $this->getDoctrine()->getRepository('TallyBundle:Category');
        $emHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead');
        $form = $this->createForm(new TallyCategoryType($em,$emHead,$globalOption), $entity, array(
            'action' => $this->generateUrl('tallycategory_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));


        return $form;
    }
    /**
     * Edits an existing TallyCategory entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $entity = $em->getRepository('TallyBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TallyCategory entity.');
        }

        $globalOption = $this->getUser()->getGlobalOption();
        $editForm = $this->createEditForm($entity,$globalOption);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            $this->getDoctrine()->getRepository('TallyBundle:CategoryMeta')->pageMeta($entity,$data);
            return $this->redirect($this->generateUrl('tallycategory_edit',array('id'=>$entity->getId())));
        }

        return $this->render('TallyBundle:TallyCategory:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a TallyCategory entity.
     *
     */
    public function deleteAction(Category $entity)
    {
        $em = $this->getDoctrine()->getManager();
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
        return $this->redirect($this->generateUrl('TallyCategory'));
    }


    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('TallyBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }
        $status = $entity->isStatus();
        if($status == 1){
            $entity->setStatus(0);
        } else{
            $entity->setStatus(1);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('TallyCategory'));
    }

    public function deleteMetaAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ProductProductBundle:CategoryMeta')->find($id);

        if ($entity) {
            $em->remove($entity);
            $em->flush();
            return new Response('success');
        }
        return new Response('invalid');
        exit;
    }



}
