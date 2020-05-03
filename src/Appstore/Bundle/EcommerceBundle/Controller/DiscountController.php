<?php

namespace Appstore\Bundle\EcommerceBundle\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\Discount;
use Appstore\Bundle\EcommerceBundle\Form\DiscountType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Discount controller.
 *
 */
class DiscountController extends Controller
{

    /**
     * Lists all Discount entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $ecommerceConfig = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $entities = $em->getRepository('EcommerceBundle:Discount')->findBy(array('ecommerceConfig'=> $ecommerceConfig),array('name'=>'asc'));

        $entity = new Discount();
        $form   = $this->createCreateForm($entity);

        return $this->render('EcommerceBundle:Discount:index.html.twig', array(
            'entities' => $entities,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    /**
     * Creates a new Discount entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Discount();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $inventory = $this->getUser()->getGlobalOption()->getEcommerceConfig();
            $entity->setEcommerceConfig($inventory);
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('ecommerce_discount'));
        }
        $ecommerceConfig = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $entities = $em->getRepository('EcommerceBundle:Discount')->findBy(array('ecommerceConfig'=>$ecommerceConfig),array('name'=>'asc'));

        return $this->render('EcommerceBundle:Discount:index.html.twig', array(
            'entities' => $entities,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Discount entity.
     *
     * @param Discount $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Discount $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $category = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new DiscountType($globalOption,$category), $entity, array(
            'action' => $this->generateUrl('ecommerce_discount_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to edit an existing PreOrder entity.
     *
     */
    public function editAction(Request $request , Discount $entity)
    {

        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }
        $entity->setName($data['value']);
        $em->flush();
        exit;

    }

    /**
     * Creates a form to edit a PreOrder entity.
     *
     * @param Discount $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Discount $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $category = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new DiscountType($globalOption,$category), $entity, array(
            'action' => $this->generateUrl('ecommerce_discount_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Edits an existing PreOrder entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EcommerceBundle:Discount')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PreOrder entity.');
        }


        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if($entity->upload()){
                $entity->removeUpload();
            }
            $entity->upload();
            $em->flush();

            return $this->redirect($this->generateUrl('ecommerce_discount'));
        }

        $ecommerceConfig = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $entities = $em->getRepository('EcommerceBundle:Discount')->findBy(array('ecommerceConfig'=>$ecommerceConfig),array('name'=>'asc'));

        return $this->render('EcommerceBundle:Discount:index.html.twig', array(
            'entities' => $entities,
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        ));
    }



    /**
     * Deletes a Discount entity.
     *
     */
    public function deleteAction( Discount $entity)
    {


        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        try {
            $entity->removeUpload();
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'error',"Data has been deleted successfully"
            );

        } catch (ForeignKeyConstraintViolationException $e) {
            $this->get('session')->getFlashBag()->add(
                'notice',"Data has been relation another Table"
            );
        }
        return $this->redirect($this->generateUrl('ecommerce_discount'));

    }

    public function statusAction(Discount $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $em = $this->getDoctrine()->getManager();
        if ($inventory != $entity->getEcommerceConfig()) {
            throw $this->createNotFoundException('Unable to find PreOrder entity.');
        }
        $status = $entity->getStatus();
        if($status == 1){
            $entity->setStatus(0);
        } else{
            $entity->setStatus(1);
        }
        $em->flush();
        if($status != 1){
            $this->getDoctrine()->getRepository('EcommerceBundle:Item')->removeDiscount($inventory->getId(),$entity->getId());
        }
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('ecommerce_discount'));
    }


    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $item = $this->getDoctrine()->getRepository('EcommerceBundle:Discount')->searchAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function searchDiscountNameAction($promotion)
    {
        return new JsonResponse(array(
            'id'=>$promotion,
            'text'=>$promotion
        ));
    }
}
