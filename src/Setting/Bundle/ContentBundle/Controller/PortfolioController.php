<?php

namespace Setting\Bundle\ContentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ContentBundle\Entity\Portfolio;
use Setting\Bundle\ContentBundle\Form\PortfolioType;

/**
 * Portfolio controller.
 *
 */
class PortfolioController extends Controller
{

    /**
     * Lists all Portfolio entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('SettingContentBundle:Portfolio')->findBy(array('globalOption'=>$globalOption));

        return $this->render('SettingContentBundle:Portfolio:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Portfolio entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Portfolio();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $data = $request->request->all();
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $entity->setGlobalOption($user->getGlobalOption());
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            $this->getDoctrine()->getRepository('SettingContentBundle:PageMeta')->portfolioPageMeta($entity,$data);
            return $this->redirect($this->generateUrl('portfolio'));
        }

        return $this->render('SettingContentBundle:Portfolio:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Portfolio entity.
     *
     * @param Portfolio $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Portfolio $entity)
    {

        $form = $this->createForm(new PortfolioType($this->getUser()->getGlobalOption()), $entity, array(
            'action' => $this->generateUrl('portfolio_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new Portfolio entity.
     *
     */
    public function newAction()
    {
        $entity = new Portfolio();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingContentBundle:Portfolio:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Portfolio entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:Portfolio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Portfolio entity.');
        }
        return $this->render('SettingContentBundle:Portfolio:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing Portfolio entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:Portfolio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Portfolio entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('SettingContentBundle:Portfolio:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }


    /**
     * Creates a form to edit a Portfolio entity.
     *
     * @param Portfolio $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Portfolio $entity)
    {

        $form = $this->createForm(new PortfolioType($this->getUser()->getGlobalOption()), $entity, array(
            'action' => $this->generateUrl('portfolio_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing Portfolio entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingContentBundle:Portfolio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Portfolio entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $data = $request->request->all();
            if($entity->upload()){
                $entity->removeUpload();
            }
            $entity->upload();
            $em->flush();
            $this->getDoctrine()->getRepository('SettingContentBundle:PageMeta')->portfolioPageMeta($entity,$data);
            return $this->redirect($this->generateUrl('portfolio_edit', array('id' => $id)));
        }

        return $this->render('SettingContentBundle:Portfolio:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }


    /**
     * Deletes a Portfolio entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entity = $em->getRepository('SettingContentBundle:Portfolio')->findOneBy(array('globalOption'=>$globalOption,'id'=>$id));
        if (!empty($entity)) {

            $entity->removeUpload();
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Status has been deleted successfully"
            );
        }else{
            $this->get('session')->getFlashBag()->add(
                'error',"Sorry! Data not deleted"
            );
        }
        return $this->redirect($this->generateUrl('portfolio'));
    }



    /**
     * Status a news entity.
     *
     */
    public function statusAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingContentBundle:Portfolio')->find($id);

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
        return $this->redirect($this->generateUrl('portfolio'));
    }


}
