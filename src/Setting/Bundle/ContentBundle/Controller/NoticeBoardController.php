<?php

namespace Setting\Bundle\ContentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ContentBundle\Entity\NoticeBoard;
use Setting\Bundle\ContentBundle\Form\NoticeBoardType;

/**
 * NoticeBoard controller.
 *
 */
class NoticeBoardController extends Controller
{

    /**
     * Lists all NoticeBoard entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('SettingContentBundle:NoticeBoard')->findBy(array('globalOption'=> $globalOption),array('created'=>'desc'));

        return $this->render('SettingContentBundle:NoticeBoard:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new NoticeBoard entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new NoticeBoard();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->get('security.context')->getToken()->getUser();
            $entity->setUser($user);
            $globalOption = $this->getUser()->getGlobalOption();
            $entity->setGlobalOption($globalOption);
            $entity->upload();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('noticeboard_show', array('id' => $entity->getId())));
        }

        return $this->render('SettingContentBundle:NoticeBoard:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a NoticeBoard entity.
     *
     * @param NoticeBoard $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(NoticeBoard $entity)
    {


        $form = $this->createForm(new NoticeBoardType(), $entity, array(
            'action' => $this->generateUrl('noticeboard_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new NoticeBoard entity.
     *
     */
    public function newAction()
    {
        $entity = new NoticeBoard();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingContentBundle:NoticeBoard:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));

    }

    /**
     * Finds and displays a NoticeBoard entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:NoticeBoard')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NoticeBoard entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingContentBundle:NoticeBoard:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing NoticeBoard entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:NoticeBoard')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NoticeBoard entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingContentBundle:NoticeBoard:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a NoticeBoard entity.
    *
    * @param NoticeBoard $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(NoticeBoard $entity)
    {

        $form = $this->createForm(new NoticeBoardType(), $entity, array(
            'action' => $this->generateUrl('noticeboard_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));

        return $form;
    }
    /**
     * Edits an existing NoticeBoard entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:NoticeBoard')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NoticeBoard entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $entity->upload();
            $entity->setSlug($this->get('setting.menuSettingRepo')->urlSlug($entity->getName()));
            $em->flush();

            return $this->redirect($this->generateUrl('noticeboard_edit', array('id' => $id)));
        }

        return $this->render('SettingContentBundle:NoticeBoard:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a NoticeBoard entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SettingContentBundle:NoticeBoard')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find NoticeBoard entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('noticeboard'));
    }

    /**
     * Creates a form to delete a NoticeBoard entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('noticeboard_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * Status a news entity.
     *
     */
    public function statusAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingContentBundle:NoticeBoard')->find($id);

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
        return $this->redirect($this->generateUrl('noticeboard'));
    }
}
