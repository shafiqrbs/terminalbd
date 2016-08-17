<?php

namespace Setting\Bundle\ContentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ContentBundle\Entity\Testimonial;
use Setting\Bundle\ContentBundle\Form\TestimonialType;

/**
 * Testimonial controller.
 *
 */
class TestimonialController extends Controller
{

    /**
     * Lists all Testimonial entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();
        $entities = $em->getRepository('SettingContentBundle:Testimonial')->findBy(array('user'=> $user),array('name' => 'asc'));

        return $this->render('SettingContentBundle:Testimonial:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Testimonial entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Testimonial();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $entity->setUser($this->getUser());
            $entity->setGlobalOption($this->getUser()->getGlobalOption());
            $entity->upload();
            $em->flush();

            return $this->redirect($this->generateUrl('testimonial'));
        }

        return $this->render('SettingContentBundle:Testimonial:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Testimonial entity.
     *
     * @param Testimonial $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Testimonial $entity)
    {

        $form = $this->createForm(new TestimonialType(), $entity, array(
            'action' => $this->generateUrl('testimonial_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
    return $form;
    }

    /**
     * Displays a form to create a new Testimonial entity.
     *
     */
    public function newAction()
    {
        $entity = new Testimonial();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingContentBundle:Testimonial:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Testimonial entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:Testimonial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Testimonial entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingContentBundle:Testimonial:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Testimonial entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:Testimonial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Testimonial entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingContentBundle:Testimonial:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Testimonial entity.
    *
    * @param Testimonial $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Testimonial $entity)
    {


        $form = $this->createForm(new TestimonialType(), $entity, array(
            'action' => $this->generateUrl('testimonial_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));


        return $form;
    }
    /**
     * Edits an existing Testimonial entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:Testimonial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Testimonial entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if($entity->upload()){
                $entity->removeUpload();
            }
            $entity->upload();
            $em->flush();

            return $this->redirect($this->generateUrl('testimonial_edit', array('id' => $id)));
        }

        return $this->render('SettingContentBundle:Testimonial:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Testimonial entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SettingContentBundle:Testimonial')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Testimonial entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('testimonial'));
    }

    /**
     * Creates a form to delete a Testimonial entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('testimonial_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
