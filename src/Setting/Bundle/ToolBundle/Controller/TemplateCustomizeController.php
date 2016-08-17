<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Doctrine\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ToolBundle\Entity\TemplateCustomize;
use Setting\Bundle\ToolBundle\Form\TemplateCustomizeType;

/**
 * TemplateCustomize controller.
 *
 */
class TemplateCustomizeController extends Controller
{

    /**
     * Lists all TemplateCustomize entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingToolBundle:TemplateCustomize')->findAll();

        return $this->render('SettingToolBundle:TemplateCustomize:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new TemplateCustomize entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new TemplateCustomize();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('templatecustomize_show', array('id' => $entity->getId())));
        }

        return $this->render('SettingToolBundle:TemplateCustomize:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a TemplateCustomize entity.
     *
     * @param TemplateCustomize $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TemplateCustomize $entity)
    {
        $form = $this->createForm(new TemplateCustomizeType(), $entity, array(
            'action' => $this->generateUrl('templatecustomize_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new TemplateCustomize entity.
     *
     */
    public function newAction()
    {
        $entity = new TemplateCustomize();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingToolBundle:TemplateCustomize:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a TemplateCustomize entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:TemplateCustomize')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TemplateCustomize entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingToolBundle:TemplateCustomize:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing TemplateCustomize entity.
     *
     */
    public function editAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingToolBundle:TemplateCustomize')->findOneBy(array('globalOption'=>$id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TemplateCustomize entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('SettingToolBundle:TemplateCustomize:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),

        ));
    }

    /**
    * Creates a form to edit a TemplateCustomize entity.
    *
    * @param TemplateCustomize $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TemplateCustomize $entity)
    {
        $form = $this->createForm(new TemplateCustomizeType(), $entity, array(
            'action' => $this->generateUrl('templatecustomize_update', array('id' => $entity->getGlobalOption()->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
                'enctype' => 'multipart/form-data',
            )
        ));

        return $form;
    }
    /**
     * Edits an existing TemplateCustomize entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:TemplateCustomize')->findOneBy(array('globalOption'=> $id));
        $data = $request->request->all();
        $file = $request->files->all();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TemplateCustomize entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            if(isset($data['removeLogo']) || (isset($file['logo']) && !empty($entity->getLogo()))  ){
                $entity->removeLogo();
                $entity->setLogo(NULL);
            }
            if(isset($data['removeHeaderImage']) || ( isset($file['removeHeaderImage']) && !empty($entity->getHeaderBgImage())) ){
                $entity->removeHeaderImage();
                $entity->setHeaderBgImage(NULL);
            }
            if(isset($data['removeBodyImage']) || ( isset($file['removeBodyImage']) && !empty($entity->getBgImage())) ){
                $entity->removeBodyImage();
                $entity->setBgImage(NULL);
            }

            $entity->upload();
            $em->flush();
            $this->getDoctrine()->getRepository('SettingToolBundle:TemplateCustomize')->fileUploader($entity,$file);
            return $this->redirect($this->generateUrl('templatecustomize_edit', array('id' => $id)));
        }

        return $this->render('SettingToolBundle:TemplateCustomize:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a TemplateCustomize entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SettingToolBundle:TemplateCustomize')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find TemplateCustomize entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('templatecustomize'));
    }

    /**
     * Creates a form to delete a TemplateCustomize entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('templatecustomize_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
