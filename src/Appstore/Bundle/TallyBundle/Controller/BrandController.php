<?php

namespace Appstore\Bundle\TallyBundle\Controller;


use Appstore\Bundle\TallyBundle\Entity\Brand;
use Appstore\Bundle\TallyBundle\Form\TallyBrandType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * TallyBrand controller.
 *
 */
class BrandController extends Controller
{

    /**
     * Lists all TallyBrand entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('Brand.php')->findBy(array('globalOption'=>$config),array('name'=>'asc'));
        return $this->render('TallyBundle:TallyBrand:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new TallyBrand entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Brand();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $config = $this->getUser()->getGlobalOption();
            $entity->setGlobalOption($config);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('TallyBrand'));
        }

        return $this->render('TallyBundle:TallyBrand:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a TallyBrand entity.
     *
     * @param Brand $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Brand $entity)
    {
        $form = $this->createForm(new TallyBrandType(), $entity, array(
            'action' => $this->generateUrl('tallybrand_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new TallyBrand entity.
     *
     */
    public function newAction()
    {
        $entity = new Brand();
        $form   = $this->createCreateForm($entity);

        return $this->render('TallyBundle:TallyBrand:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a TallyBrand entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('Brand.php')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TallyBrand entity.');
        }

       return $this->render('TallyBundle:TallyBrand:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing TallyBrand entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('Brand.php')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TallyBrand entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('TallyBundle:TallyBrand:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a TallyBrand entity.
    *
    * @param Brand $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Brand $entity)
    {
        $form = $this->createForm(new TallyBrandType(), $entity, array(
            'action' => $this->generateUrl('tallybrand_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
         return $form;
    }
    /**
     * Edits an existing TallyBrand entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('Brand.php')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TallyBrand entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('tallybrand_edit', array('id' => $id)));
        }

        return $this->render('TallyBundle:TallyBrand:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Deletes a TallyBrand entity.
     *
     */
    public function deleteAction(Ass $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Brand entity.');
        }

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

        return $this->redirect($this->generateUrl('tallybrand'));
    }

    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
          $config = $this->getUser()->getGlobalOption();
          $item = $this->getDoctrine()->getRepository('Brand.php')->searchAutoComplete($item,$config);
        }
        return new JsonResponse($item);
    }

    public function searchTallyBrandNameAction($brand)
    {
        return new JsonResponse(array(
            'id'=> $brand,
            'text'=> $brand
        ));
    }
}
