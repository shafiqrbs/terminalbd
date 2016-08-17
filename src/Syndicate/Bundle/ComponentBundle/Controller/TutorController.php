<?php

namespace Syndicate\Bundle\ComponentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Syndicate\Bundle\ComponentBundle\Entity\Tutor;
use Syndicate\Bundle\ComponentBundle\Form\TutorType;
use Knp\Snappy\Pdf;

/**
 * Tutor controller.
 *
 */
class TutorController extends Controller
{

    /**
     * Lists all Tutor entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SyndicateComponentBundle:Tutor')->findAll();

        return $this->render('SyndicateComponentBundle:Tutor:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Tutor entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Tutor();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tutor_show', array('id' => $entity->getId())));
        }

        return $this->render('SyndicateComponentBundle:Tutor:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Tutor entity.
     *
     * @param Tutor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Tutor $entity)
    {
        $em = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $syn = $this->getDoctrine()->getRepository('SettingToolBundle:Syndicate');
        $form = $this->createForm(new TutorType($em,$syn), $entity, array(
            'action' => $this->generateUrl('tutor_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Tutor entity.
     *
     */
    public function newAction()
    {
        $entity = new Tutor();
        $form   = $this->createCreateForm($entity);

        return $this->render('SyndicateComponentBundle:Tutor:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Tutor entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SyndicateComponentBundle:Tutor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tutor entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SyndicateComponentBundle:Tutor:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Tutor entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SyndicateComponentBundle:Tutor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tutor entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SyndicateComponentBundle:Tutor:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Tutor entity.
    *
    * @param Tutor $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Tutor $entity)
    {
        $em = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $syn = $this->getDoctrine()->getRepository('SettingToolBundle:Syndicate');

        $form = $this->createForm(new TutorType($em, $syn), $entity, array(
            'action' => $this->generateUrl('tutor_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing Tutor entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $entity = $em->getRepository('SyndicateComponentBundle:Tutor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tutor entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $entity->upload();
            $em->flush();

            $this->getDoctrine()->getRepository('SyndicateComponentBundle:Academic')->insertAcademic($entity,$data);
            $this->getDoctrine()->getRepository('SyndicateComponentBundle:AcademicMeta')->insertAcademicMeta($entity,$data);

            return $this->redirect($this->generateUrl('tutor_edit', array('id' => $id)));
        }

        return $this->render('SyndicateComponentBundle:Tutor:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Tutor entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SyndicateComponentBundle:Tutor')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Tutor entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('tutor'));
    }

    /**
     * Creates a form to delete a Tutor entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tutor_delete', array('id' => $id)))
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
        $entity = $em->getRepository('SyndicateComponentBundle:Tutor')->find($id);

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
        return $this->redirect($this->generateUrl('Tutor'));
    }

    public function printDetailsAction(Request $request, Tutor $Tutor)
    {

        return $this->render('SyndicateComponentBundle:Tutor:print.html.twig', array(
            'entity'      => $Tutor

        ));

    }
    public function pdfDetailsAction(Request $request, Tutor $Tutor)
    {

        $html = $this->renderView(
            'SyndicateComponentBundle:Tutor:print.html.twig', array(
                'entity' => $Tutor
            )
        );

        $wkhtmltopdfPath = '/usr/local/bin/wkhtmltopdf'; /* check mac pdf */
        //$wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver'; /* check server pdf */
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Tutor.pdf"');
        echo $pdf;

        return new Response('');

    }

    /**
     * Displays a form to edit an existing Tutor entity.
     *
     */
    public function modifyAction()
    {

        $user = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $this->getDoctrine()->getRepository('SyndicateComponentBundle:Tutor')->insertVendor($user);
        $entity = $em->getRepository('SyndicateComponentBundle:Tutor')->findOneBy(array('user'=>$user));
        $editForm = $this->createEditForm($entity);
        return $this->render('SyndicateComponentBundle:Tutor:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView()

        ));

    }


}
