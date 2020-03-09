<?php

namespace Appstore\Bundle\RestaurantBundle\Controller;

use Appstore\Bundle\RestaurantBundle\Entity\Particular;
use Appstore\Bundle\RestaurantBundle\Entity\ProductionElement;
use Appstore\Bundle\RestaurantBundle\Entity\ProductionValueAdded;
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
class ProductionController extends Controller
{


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
        $productionValues = $this->getDoctrine()->getRepository('RestaurantBundle:ProductionValueAdded')->getProductionAdded($entity);
        return $this->render('RestaurantBundle:Product:production.html.twig', array(
            'entity'      => $entity,
            'particulars' => $particulars,
            'productionValues' => $productionValues,
            'form'   => $editForm->createView(),
        ));
    }

    public function productionUpdateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $entity = $em->getRepository('RestaurantBundle:Particular')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
        $productionPrice = $entity->getValueAddedAmount() + $entity->getProductionElementAmount();
        $entity->setPurchasePrice($productionPrice);
        $em->flush();
        return $this->redirect($this->generateUrl('restaurant_product'));
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
        $this->getDoctrine()->getRepository('RestaurantBundle:ProductionElement')->insertProductionElement($invoice, $invoiceItems);
        $subTotal = $this->getDoctrine()->getRepository('RestaurantBundle:ProductionElement')->getProductionPrice($invoice);
        $invoice->setProductionElementAmount($subTotal);
        $em->flush();
        $invoiceParticulars = $this->getDoctrine()->getRepository('RestaurantBundle:ProductionElement')->particularProductionElements($invoice);
        $result = array('subTotal' => $subTotal , 'invoiceParticulars' => $invoiceParticulars);
        return new Response(json_encode($result));

    }

    public function itemValueAddAction(Request $request, ProductionValueAdded $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $amount = $_REQUEST['amount'];
        $entity->setAmount($amount);
        $em->flush();
        $productionItem = $entity->getProductionItem();
        $this->getDoctrine()->getRepository('RestaurantBundle:ProductionElement')->particularProductionElements($productionItem);
        $subTotal = $this->getDoctrine()->getRepository('RestaurantBundle:ProductionValueAdded')->totalValues($productionItem);
        $productionItem->setValueAddedAmount($subTotal);
        $em->flush();
        return new Response('success');

    }

    public function productionElementDeleteAction(Particular $product, ProductionElement $particular){

        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($particular);
        $em->flush();
        $subTotal = $this->getDoctrine()->getRepository('RestaurantBundle:ProductionElement')->getProductionPrice($product);
        $product->setValueAddedAmount($subTotal);
        $em->flush();
        $invoiceParticulars = $this->getDoctrine()->getRepository('RestaurantBundle:ProductionElement')->particularProductionElements($product);
        $result = array('subTotal' => $subTotal,'invoiceParticulars' => $invoiceParticulars);
        return new Response(json_encode($result));

    }

    public function sortingAction()
    {
        $entity = new Particular();
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $pagination = $em->getRepository('RestaurantBundle:Particular')->productSortingList($config,array('product','stockable'));
        $editForm = $this->createCreateForm($entity);
        return $this->render('RestaurantBundle:Product:sorting.html.twig', array(
            'pagination' => $pagination,
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
