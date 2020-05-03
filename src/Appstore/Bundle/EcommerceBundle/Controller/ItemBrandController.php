<?php

namespace Appstore\Bundle\EcommerceBundle\Controller;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\ItemBrand;
use Appstore\Bundle\EcommerceBundle\Form\ItemBrandType;

/**
 * ItemBrand controller.
 *
 */
class ItemBrandController extends Controller
{

    /**
     * Lists all ItemBrand entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $entities = $em->getRepository('EcommerceBundle:ItemBrand')->findBy(array('ecommerceConfig'=>$config),array('name'=>'asc'));

        return $this->render('EcommerceBundle:ItemBrand:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new ItemBrand entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new ItemBrand();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $config = $this->getUser()->getGlobalOption()->getEcommerceConfig();
	        $entity->setEcommerceConfig($config);
	        $entity->upload();
	        $em->persist($entity);
	        $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('ecommerce_brand'));
        }

        return $this->render('EcommerceBundle:ItemBrand:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ItemBrand entity.
     *
     * @param ItemBrand $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ItemBrand $entity)
    {
        $form = $this->createForm(new ItemBrandType(), $entity, array(
            'action' => $this->generateUrl('ecommerce_brand_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new ItemBrand entity.
     *
     */
    public function newAction()
    {
        $entity = new ItemBrand();
        $form   = $this->createCreateForm($entity);

        return $this->render('EcommerceBundle:ItemBrand:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ItemBrand entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ndle:ItemBrand')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ItemBrand entity.');
        }

       return $this->render('EcommerceBundle:ItemBrand:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing ItemBrand entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EcommerceBundle:ItemBrand')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ItemBrand entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render('EcommerceBundle:ItemBrand:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a ItemBrand entity.
    *
    * @param ItemBrand $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ItemBrand $entity)
    {
        $form = $this->createForm(new ItemBrandType(), $entity, array(
            'action' => $this->generateUrl('ecommerce_brand_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
         return $form;
    }
    /**
     * Edits an existing ItemBrand entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EcommerceBundle:ItemBrand')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ItemBrand entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if($entity->upload()){
                $entity->removeUpload();
                $entity->upload();
            }else{
                $entity->upload();
            }
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('ecommerce_brand_edit', array('id' => $id)));
        }

        return $this->render('EcommerceBundle:ItemBrand:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    public function statusAction(ItemBrand $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $em = $this->getDoctrine()->getManager();
        $status = $entity->getStatus();
        if ($inventory != $entity->getEcommerceConfig()) {
            throw $this->createNotFoundException('Unable to find PreOrder entity.');
        }
        if($status == 1){
            $entity->setStatus(0);
            $this->getDoctrine()->getRepository('EcommerceBundle:Item')->updateBrandItem($entity,0);
        } else{
            $this->getDoctrine()->getRepository('EcommerceBundle:Item')->updateBrandItem($entity,1);
            $entity->setStatus(1);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('ecommerce_brand'));
    }

    public function featureAction(ItemBrand $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $em = $this->getDoctrine()->getManager();
        $status = $entity->getFeature();
        if ($inventory != $entity->getEcommerceConfig()) {
            throw $this->createNotFoundException('Unable to find PreOrder entity.');
        }
        if($status == 1){
            $entity->setFeature(0);
        } else{
            $entity->setFeature(1);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('ecommerce_brand'));
    }


    /**
     * Deletes a ItemBrand entity.
     *
     */
    public function deleteAction(ItemBrand $entity)
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

        return $this->redirect($this->generateUrl('ecommerce_brand'));
    }

    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $config = $this->getUser()->getGlobalOption()->getEcommerceConfig();
            $item = $this->getDoctrine()->getRepository('EcommerceBundle:ItemBrand')->searchAutoComplete($item,$config);
        }
        return new JsonResponse($item);
    }

    public function searchItemBrandNameAction($brand)
    {
        return new JsonResponse(array(
            'id'=> $brand,
            'text'=> $brand
        ));
    }
}
