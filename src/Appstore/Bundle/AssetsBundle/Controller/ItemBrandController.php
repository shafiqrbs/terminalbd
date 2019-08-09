<?php

namespace Appstore\Bundle\AssetsBundle\Controller;


use Appstore\Bundle\AssetsBundle\Entity\AssetsItemBrand;
use Appstore\Bundle\AssetsBundle\Form\AssetsItemBrandType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * AssetItemBrand controller.
 *
 */
class ItemBrandController extends Controller
{

    /**
     * Lists all AssetItemBrand entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('AssetsBundle:AssetsItemBrand')->findBy(array('globalOption'=>$config),array('name'=>'asc'));
        return $this->render('AssetsBundle:AssetItemBrand:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new AssetItemBrand entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new AssetsItemBrand();
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
            return $this->redirect($this->generateUrl('assetsitembrand'));
        }

        return $this->render('AssetsBundle:AssetItemBrand:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a AssetItemBrand entity.
     *
     * @param AssetItemBrand $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AssetsItemBrand $entity)
    {
        $form = $this->createForm(new AssetsItemBrandType(), $entity, array(
            'action' => $this->generateUrl('assetsitembrand_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new AssetItemBrand entity.
     *
     */
    public function newAction()
    {
        $entity = new AssetsItemBrand();
        $form   = $this->createCreateForm($entity);

        return $this->render('AssetsBundle:AssetItemBrand:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a AssetItemBrand entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AssetsBundle:AssetItemBrand')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AssetItemBrand entity.');
        }

       return $this->render('AssetsBundle:AssetItemBrand:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing AssetItemBrand entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AssetsBundle:AssetItemBrand')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AssetItemBrand entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('AssetsBundle:AssetItemBrand:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a AssetItemBrand entity.
    *
    * @param AssetItemBrand $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AssetItemBrand $entity)
    {
        $form = $this->createForm(new AssetsItemBrandType(), $entity, array(
            'action' => $this->generateUrl('assetsitembrand_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
         return $form;
    }
    /**
     * Edits an existing AssetItemBrand entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AssetsBundle:AssetItemBrand')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AssetItemBrand entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('assetsitembrand_edit', array('id' => $id)));
        }

        return $this->render('AssetsBundle:AssetItemBrand:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Deletes a AssetItemBrand entity.
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

        return $this->redirect($this->generateUrl('assetsitembrand'));
    }

    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
          $config = $this->getUser()->getGlobalOption();
          $item = $this->getDoctrine()->getRepository('AssetsBundle:AssetItemBrand')->searchAutoComplete($item,$config);
        }
        return new JsonResponse($item);
    }

    public function searchAssetItemBrandNameAction($brand)
    {
        return new JsonResponse(array(
            'id'=> $brand,
            'text'=> $brand
        ));
    }
}
