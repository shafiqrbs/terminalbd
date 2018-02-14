<?php

namespace Appstore\Bundle\MedicineBundle\Controller;

use Appstore\Bundle\MedicineBundle\Form\MedicineType;
use Appstore\Bundle\MedicineBundle\Entity\MedicineBrand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * MedicineBrand controller.
 *
 */
class MedicineController extends Controller
{

    /**
     * Lists all MedicineBrand entities.
     *
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('MedicineBundle:MedicineBrand')->findBy(array('globalOption' => $option->getSlug()));
        $entity = new MedicineBrand();
        $form   = $this->createCreateForm($entity);
        return $this->render('MedicineBundle:MedicineBrand:medicine.html.twig', array(
            'pagination' => $entities,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));

    }

    /**
     * Creates a new MedicineBrand entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new MedicineBrand();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $entities = $em->getRepository('MedicineBundle:MedicineBrand')->findBy(array('globalOption' => $option->getSlug()));

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('medicine_user'));
        }
        return $this->render('MedicineBundle:MedicineBrand:medicine.html.twig', array(
            'entities' => $entities,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a MedicineBrand entity.
     *
     * @param MedicineBrand $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MedicineBrand $entity)
    {
        $form = $this->createForm(new MedicineType(), $entity, array(
            'action' => $this->generateUrl('medicine_user_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    /**
     * Displays a form to edit an existing MedicineBrand entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MedicineBundle:MedicineBrand')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicineBrand entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('MedicineBundle:MedicineBrand:medicine.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a MedicineBrand entity.
    *
    * @param MedicineBrand $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(MedicineBrand $entity)
    {
        $form = $this->createForm(new MedicineType(), $entity, array(
            'action' => $this->generateUrl('medicine_user_update', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;


    }
    /**
     * Edits an existing MedicineBrand entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MedicineBundle:MedicineBrand')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicineBrand entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            return $this->redirect($this->generateUrl('medicine_user_edit', array('id' => $id)));
        }

        return $this->render('MedicineBundle:MedicineBrand:medicine.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

}
