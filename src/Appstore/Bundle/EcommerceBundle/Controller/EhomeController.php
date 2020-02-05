<?php

namespace Appstore\Bundle\EcommerceBundle\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\Ehome;
use Appstore\Bundle\EcommerceBundle\Form\EhomeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Appstore\Bundle\EcommerceBundle\Entity\Discount;
use Symfony\Component\HttpFoundation\Request;


/**
 * FeatureWidget controller.
 *
 */
class EhomeController extends Controller
{

    /**
     * Lists all FeatureWidget entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('EcommerceBundle:Template')->findBy(array('ecommerceConfig'=> $globalOption->getEcommerceConfig()),array('sorting'=>'asc'));

        return $this->render('EcommerceBundle:FeatureWidget:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new FeatureWidget entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Ehome();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $entity->setEcommerceConfig($user->getGlobalOption()->getEcommerceConfig());
            $entity->upload();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ecommerceslider'));
        }

        return $this->render('EcommerceBundle:FeatureWidget:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a FeatureWidget entity.
     *
     * @param FeatureWidget $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Ehome $entity)
    {

        $globalOption = $this->getUser()->getGlobalOption();
        $category = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new EhomeType($globalOption,$category), $entity, array(
            'action' => $this->generateUrl('ecommerceslider_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new FeatureWidget entity.
     *
     */
    public function newAction()
    {
        $entity = new Ehome();
        $form   = $this->createCreateForm($entity);

        return $this->render('EcommerceBundle:FeatureWidget:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a FeatureWidget entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EcommerceBundle:FeatureWidget')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FeatureWidget entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EcommerceBundle:FeatureWidget:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing FeatureWidget entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EcommerceBundle:FeatureWidget')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FeatureWidget entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EcommerceBundle:FeatureWidget:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Creates a form to edit a FeatureWidget entity.
     *
     * @param FeatureWidget $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Ehome $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $category = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new Ehome($globalOption,$category), $entity, array(
            'action' => $this->generateUrl('ecommerceslider_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing FeatureWidget entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EcommerceBundle:FeatureWidget')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FeatureWidget entity.');
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

            return $this->redirect($this->generateUrl('ecommerceslider_edit', array('id' => $id)));
        }

        return $this->render('EcommerceBundle:FeatureWidget:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Deletes a FeatureWidget entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entity = $em->getRepository('EcommerceBundle:FeatureWidget')->findOneBy(array('globalOption'=>$globalOption,'id'=>$id));
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
        return $this->redirect($this->generateUrl('ecommerceslider'));
    }

    /**
     * Creates a form to delete a FeatureWidget entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ecommerceslider_delete', array('id' => $id)))
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
        $entity = $em->getRepository('EcommerceBundle:FeatureWidget')->find($id);

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
        return $this->redirect($this->generateUrl('ecommerceslider'));
    }

    public function sortingAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        $entities = $em->getRepository('EcommerceBundle:FeatureWidget')->findBy(array('user'=>$user),array('sorting'=>'asc'));

        return $this->render('EcommerceBundle:FeatureWidget:sorting.html.twig', array(
            'entities' => $entities,

        ));

    }

    public function sortedAction(Request $request)
    {
        $data = $request ->request->get('item');
        $this->getDoctrine()->getRepository('EcommerceBundle:FeatureWidget')->setDivOrdering($data);
        exit;

    }
}
