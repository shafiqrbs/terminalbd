<?php

namespace Appstore\Bundle\RestaurantBundle\Controller;

use Appstore\Bundle\RestaurantBundle\Entity\Particular;
use Appstore\Bundle\RestaurantBundle\Form\ParticularType;
use Appstore\Bundle\RestaurantBundle\Form\ProductionType;
use Appstore\Bundle\RestaurantBundle\Form\ProductType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


/**
 * ParticularController controller.
 *
 */
class ProductController extends Controller
{

    public function paginate($entities)
    {
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            50  /*limit per page*/
        );
        return $pagination;
    }


    /**
     * Lists all Particular entities.
     *
     */
    public function indexAction()
    {
        $entity = new Particular();
        $data = $_REQUEST;
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $pagination = $em->getRepository('RestaurantBundle:Particular')->findWithSearch($config,array('product'));
        $editForm = $this->createCreateForm($entity);
        return $this->render('RestaurantBundle:Product:index.html.twig', array(
            'pagination' => $pagination,
            'searchForm' => $data,
            'entity' => $entity,
            'form'   => $editForm->createView(),
        ));

    }



    /**
     * Creates a new Particular entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Particular();
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $pagination = $em->getRepository('RestaurantBundle:Particular')->findWithSearch($config,array('product'));
        //$pagination = $this->paginate($pagination);
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setRestaurantConfig($config);
            $service = $this->getDoctrine()->getRepository('RestaurantBundle:Service')->findOneBy(array('slug'=>'product'));
            $entity->setService($service);
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('restaurant_product'));
        }

        return $this->render('RestaurantBundle:Product:index.html.twig', array(
            'entity' => $entity,
            'pagination' => $pagination,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Particular entity.
     *
     * @param Particular $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Particular $entity)
    {
        $form = $this->createForm(new ProductType(), $entity, array(
            'action' => $this->generateUrl('restaurant_product_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }



    /**
     * Displays a form to edit an existing Particular entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $pagination = $em->getRepository('RestaurantBundle:Particular')->findWithSearch($config,array('product'));
        //$pagination = $this->paginate($pagination);
        $entity = $em->getRepository('RestaurantBundle:Particular')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
        $editForm = $this->createEditForm($entity);

        return $this->render('RestaurantBundle:Product:index.html.twig', array(
            'entity'      => $entity,
            'pagination'      => $pagination,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Particular entity.
     *
     * @param Particular $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Particular $entity)
    {

        $form = $this->createForm(new ProductType(), $entity, array(
            'action' => $this->generateUrl('restaurant_product_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing Particular entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $pagination = $em->getRepository('RestaurantBundle:Particular')->findWithSearch($config,array('product'));
        //$pagination = $this->paginate($pagination);
        $entity = $em->getRepository('RestaurantBundle:Particular')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if($entity->upload() && !empty($entity->getFile())){
                $entity->removeUpload();
            }
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('restaurant_product'));
        }

        return $this->render('RestaurantBundle:Product:index.html.twig', array(
            'entity'      => $entity,
            'pagination'      => $pagination,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a Particular entity.
     *
     */
    public function deleteAction(Particular $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
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
        return $this->redirect($this->generateUrl('restaurant_product'));
    }

   
    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Particular $entity)
    {

        $em = $this->getDoctrine()->getManager();
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
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('restaurant_product'));
    }

    /**
     * Creates a form to edit a Particular entity.
     *
     * @param Particular $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createProductionCostingForm(Particular $entity)
    {

        $form = $this->createForm(new ProductionType(), $entity, array(
            'action' => $this->generateUrl('restaurant_production_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    public function productionAction(Particular $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $editForm = $this->createProductionCostingForm($entity);
        $particulars = $em->getRepository('RestaurantBundle:Particular')->getMedicineParticular($config);
        return $this->render('RestaurantBundle:Product:production.html.twig', array(
            'entity'      => $entity,
            'particulars' => $particulars,
            'form'   => $editForm->createView(),
        ));
    }

    public function productionUpdateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $pagination = $em->getRepository('RestaurantBundle:Particular')->findWithSearch($config,array('product'));
        //$pagination = $this->paginate($pagination);
        $entity = $em->getRepository('RestaurantBundle:Particular')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if($entity->upload() && !empty($entity->getFile())){
                $entity->removeUpload();
            }
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('restaurant_product'));
        }

        return $this->render('RestaurantBundle:Product:index.html.twig', array(
            'entity'      => $entity,
            'pagination'      => $pagination,
            'form'   => $editForm->createView(),
        ));
    }


    public function particularSearchAction(Particular $particular)
    {
        return new Response(json_encode(array('particularId'=> $particular->getId() ,'price'=> $particular->getPrice() , 'purchasePrice'=> $particular->getPurchasePrice(), 'quantity'=> 1 , 'minimumPrice'=> '', 'instruction'=>'')));
    }

    public function addParticularAction(Request $request, Particular $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $particularId = $request->request->get('particularId');
        $quantity = $request->request->get('quantity');
        $price = $request->request->get('price');
        $invoiceItems = array('particularId' => $particularId , 'quantity' => $quantity,'price' => $price );
        $this->getDoctrine()->getRepository('RestaurantBundle:PurchaseItem')->insertPurchaseItems($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository('RestaurantBundle:Purchase')->updatePurchaseTotalPrice($invoice);
        $invoiceParticulars = $this->getDoctrine()->getRepository('RestaurantBundle:PurchaseItem')->getPurchaseItems($invoice);
        $msg = 'Particular added successfully';

        $subTotal = $invoice->getSubTotal() > 0 ? $invoice->getSubTotal() : 0;
        $grandTotal = $invoice->getNetTotal() > 0 ? $invoice->getNetTotal() : 0;
        $dueAmount = $invoice->getDue() > 0 ? $invoice->getDue() : 0;

        return new Response(json_encode(array('subTotal' => $subTotal,'grandTotal' => $grandTotal,'dueAmount' => $dueAmount, 'vat' => '','invoiceParticulars' => $invoiceParticulars, 'msg' => $msg )));
        exit;
    }

    public function productionElementDeleteAction(Purchase $invoice, PurchaseItem $particular){

        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }

        $em->remove($particular);
        $em->flush();
        $invoice = $this->getDoctrine()->getRepository('RestaurantBundle:Purchase')->updatePurchaseTotalPrice($invoice);
        $invoiceParticulars = $this->getDoctrine()->getRepository('RestaurantBundle:PurchaseItem')->getPurchaseItems($invoice);

        $msg = 'Particular deleted successfully';
        $subTotal = $invoice->getSubTotal() > 0 ? $invoice->getSubTotal() : 0;
        $grandTotal = $invoice->getNetTotal() > 0 ? $invoice->getNetTotal() : 0;
        $dueAmount = $invoice->getDue() > 0 ? $invoice->getDue() : 0;
        return new Response(json_encode(array('subTotal' => $subTotal,'grandTotal' => $grandTotal,'dueAmount' => $dueAmount, 'vat' => '','invoiceParticular' => $invoiceParticulars, 'msg' => $msg )));
        exit;


    }

    public function sortingAction()
    {
        $entity = new Particular();
        $data = $_REQUEST;
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $pagination = $em->getRepository('RestaurantBundle:Particular')->findWithSearch($config,array('product','stockable'));
        $editForm = $this->createCreateForm($entity);
        return $this->render('RestaurantBundle:Product:sorting.html.twig', array(
            'pagination' => $pagination,
            'searchForm' => $data,
            'entity' => $entity,
            'form'   => $editForm->createView(),
        ));

    }

    public function sortedAction(Request $request)
    {
        $data = $request ->request->get('item');
        $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->setProductSorting($data);
        exit;
    }

}
