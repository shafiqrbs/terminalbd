<?php

namespace Syndicate\Bundle\ComponentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Syndicate\Bundle\ComponentBundle\Entity\Education;
use Syndicate\Bundle\ComponentBundle\Form\EducationType;
use Knp\Snappy\Pdf;

/**
 * Education controller.
 *
 */
class EducationController extends Controller
{

    /**
     * Lists all Education entities.
     *
     */

    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $keyword = $request -> query->get('search');
        $sort = $this->get('request')->query->get('sort','name');
        $direction =$this->get('request')->query->get('direction','asc');
        $entities = $em->getRepository('SyndicateComponentBundle:Education')->findBy(array(),array( $sort => $direction ));

        $pagination = $this->paginate($entities);

        return $this->render('SyndicateComponentBundle:Education:index.html.twig', array(
            'pagination' => $pagination
        ));

    }

    public function deleteListAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SyndicateComponentBundle:Education')->findBy(array(),array('name'=>'asc'));

        $pagination = $this->paginate($entities);

        return $this->render('SyndicateComponentBundle:Education:delete.html.twig', array(
            'pagination' => $pagination
        ));

    }



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
     * Creates a new Education entity.
     *
     */
    public function createAction(Request $request)
    {

        $user = $this->get('security.context')->getToken()->getUser();

        $entity = new Education();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $entity->upload();
            $entity->setUser($user);

            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('education_show', array('id' => $entity->getId())));
        }

        return $this->render('SyndicateComponentBundle:Education:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Education entity.
     *
     * @param Education $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Education $entity)
    {
        $em = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $ic = $this->getDoctrine()->getRepository('SettingToolBundle:InstituteLevel');

        $form = $this->createForm(new EducationType($em,$ic), $entity, array(
            'action' => $this->generateUrl('education_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new Education entity.
     *
     */
    public function newAction()
    {
        $entity = new Education();
        $form   = $this->createCreateForm($entity);

        return $this->render('SyndicateComponentBundle:Education:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Education entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SyndicateComponentBundle:Education')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Education entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SyndicateComponentBundle:Education:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Education entity.
     *
     */
    public function editAction($id)
    {

        //$this->getDoctrine()->getRepository('SyndicateComponentBundle:Education')->getGeoCode();

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SyndicateComponentBundle:Education')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Education entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        $instituteLevel = $this->getInstituteUnderVendor($entity);

        return $this->render('SyndicateComponentBundle:Education:new.html.twig', array(
            'entity'      => $entity,
            'instituteLevel'      => $instituteLevel,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Education entity.
     *
     */
    public function modifyAction()
    {

        $id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();

        $this->getDoctrine()->getRepository('SyndicateComponentBundle:Education')->insertVendor($id);
        $entity = $em->getRepository('SyndicateComponentBundle:Education')->findOneBy(array('user'=>$id));

        $editForm = $this->createEditForm($entity);
        $instituteLevel = $this->getInstituteUnderVendor($entity);

        return $this->render('SyndicateComponentBundle:Education:new.html.twig', array(
            'entity'      => $entity,
            'instituteLevel'      => $instituteLevel,
            'form'   => $editForm->createView()

        ));
    }

    /**
     * Creates a form to edit a Education entity.
     *
     * @param Education $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Education $entity)
    {
        $em = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $ic = $this->getDoctrine()->getRepository('SettingToolBundle:InstituteLevel');

        $form = $this->createForm(new EducationType($em,$ic), $entity, array(
            'action' => $this->generateUrl('education_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing Education entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SyndicateComponentBundle:Education')->find($id);
        $instituteLevels = $this->get('request')->request->get('subinstituteLevels');

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Education entity.');
        }
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $entity->upload();
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );

            $entity = $em->getRepository('SyndicateComponentBundle:Education')->setUpdateInstituteLevel($entity,$instituteLevels);
            return $this->redirect($this->generateUrl('education_edit', array('id' => $id)));
        }
        $instituteLevel = $this->getInstituteUnderVendor($entity);

        return $this->render('SyndicateComponentBundle:Education:new.html.twig', array(
            'entity'      => $entity,
            'instituteLevel'      => $instituteLevel,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Education entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SyndicateComponentBundle:Education')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Education entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('education'));
    }

    /**
     * Creates a form to delete a Education entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('education_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
            ;
    }

    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        //$data = $request->request->all();


        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SyndicateComponentBundle:Education')->find($id);

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
            'error',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('education'));
    }

    public function printDetailsAction(Request $request, Education $education)
    {

        return $this->render('SyndicateComponentBundle:Education:print.html.twig', array(
            'entity'      => $education

        ));

    }
    public function pdfDetailsAction(Request $request, Education $education)
    {

        $html = $this->renderView(
            'SyndicateComponentBundle:Education:print.html.twig', array(
                'entity' => $education
            )
        );

        $wkhtmltopdfPath = '/usr/local/bin/wkhtmltopdf'; /* check mac pdf */
        //$wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver'; /* check server pdf */
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="education.pdf"');
        echo $pdf;

        return new Response('');

    }

    public function getInstituteUnderVendor($entity)
    {
        $em = $this->getDoctrine()->getManager();
        $institutes     = $em->getRepository('SettingToolBundle:InstituteLevel')->findBy(array('status'=>1,'parent'=>NULL),array('name'=>'asc'));
        return $subSyndicates  = $this->getDoctrine()->getRepository('SettingToolBundle:InstituteLevel')->getSelectedInstitutes($institutes,$entity);

    }




}
