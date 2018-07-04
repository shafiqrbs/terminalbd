<?php
namespace Appstore\Bundle\BusinessBundle\Controller;

use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessProductionElement;
use Appstore\Bundle\BusinessBundle\Entity\Particular;
use Appstore\Bundle\BusinessBundle\Form\ParticularType;
use Appstore\Bundle\BusinessBundle\Form\ProductionType;
use Appstore\Bundle\BusinessBundle\Form\ProductType;
use Appstore\Bundle\BusinessBundle\Form\StockType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


/**
 * ProductionController controller.
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
    private function createProductionCostingForm(BusinessParticular $entity)
    {

        $form = $this->createForm(new ProductionType(), $entity, array(
            'action' => $this->generateUrl('business_production_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
                'id' => 'production',
            )
        ));
        return $form;
    }

    public function productionAction(BusinessParticular  $entity)
    {

        $em = $this->getDoctrine()->getManager();
        $config =$this->getUser()->getGlobalOption()->getBusinessConfig()->getId();
        if($entity->getProductType() != 'production' and $entity->getBusinessConfig()->getId() != $config){
            return $this->redirect($this->generateUrl('business_stock'));
        }
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $editForm = $this->createProductionCostingForm($entity);
        $particulars = $em->getRepository('BusinessBundle:BusinessParticular')->getFindWithParticular($config,$type = array('consumable','stock'));
        return $this->render('BusinessBundle:Production:production.html.twig', array(
            'entity'      => $entity,
            'particulars' => $particulars,
            'form'   => $editForm->createView(),
        ));
    }

    public function preProductionAction(){

    }

    public function productionUpdateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BusinessBundle:BusinessParticular')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
        $editForm = $this->createProductionCostingForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if($entity->upload() && !empty($entity->getFile())){
                $entity->removeUpload();
            }
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->updateSalesPrice($entity);
            return $this->redirect($this->generateUrl('business_stock'));
        }

        return $this->render('BusinessBundle:Production:production.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }


    public function particularSearchAction(BusinessParticular $particular)
    {
        return new Response(json_encode(array('particularId'=> $particular->getId() ,'price'=> $particular->getPrice() , 'purchasePrice'=> $particular->getPurchasePrice(), 'quantity'=> 1 , 'minimumPrice'=> '')));
    }

    public function addParticularAction(Request $request, BusinessParticular $particular)
    {
        $em = $this->getDoctrine()->getManager();
        $particularId = $request->request->get('particularId');
        $quantity = $request->request->get('quantity');
        $price = $request->request->get('price');
        $data = array('particularId' => $particularId , 'quantity' => $quantity,'price' => $price );
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessProductionElement')->insertProductionElement($particular, $data);
        $salesPrice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->updateSalesPrice($particular);
        $production = $this->getDoctrine()->getRepository('BusinessBundle:BusinessProductionElement')->particularProductionElements($particular);
        return new Response(json_encode(array('subTotal'=>$salesPrice,'particulars' => $production)));
        exit;
    }

    public function deleteAction(BusinessParticular $particular, BusinessProductionElement $element){

        $em = $this->getDoctrine()->getManager();
        if (!$element) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($element);
        $em->flush();
        $salesPrice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->updateSalesPrice($particular);
        return new Response(json_encode(array('subTotal'=>$salesPrice,'particulars' => '')));
        exit;


    }

    public function sortingAction()
    {
        $entity = new Particular();
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $pagination = $em->getRepository('BusinessBundle:Particular')->productSortingList($config,array('product','stockable'));
        $editForm = $this->createCreateForm($entity);
        return $this->render('BusinessBundle:Product:sorting.html.twig', array(
            'pagination' => $pagination,
            'entity' => $entity,
            'form'   => $editForm->createView(),
        ));

    }

    public function sortedAction(Request $request)
    {
        $data = $request ->request->get('item');
        $this->getDoctrine()->getRepository('BusinessBundle:Particular')->setProductSorting($data);
        exit;
    }

}
