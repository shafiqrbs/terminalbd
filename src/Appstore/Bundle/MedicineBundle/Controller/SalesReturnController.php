<?php

namespace Appstore\Bundle\MedicineBundle\Controller;

use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesReturn;
use Appstore\Bundle\MedicineBundle\Form\MedicineSalesReturnType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * SalesReturn controller.
 *
 */
class SalesReturnController extends Controller
{

    /**
     * Lists all SalesReturn entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesReturn')->findBy(array('medicineConfig' => $config),array('created'=>'DESC'));
        return $this->render('MedicineBundle:SalesReturn:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    public function newAction(MedicineSalesItem $item){

        return $this->render('MedicineBundle:SalesReturn:new.html.twig', array(
            'entity' => $item->getMedicineSales(),
            'salesItem' => $item,
        ));
    }

    /**
     * Creates a new SalesReturn entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $data = $request->request->all();
        foreach ($data['quantity'] as $key => $qnt) {
        	if($qnt > 0 ){
		        $entity = new MedicineSalesReturn();
		        $salesItem = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesItem')->find($data['salesItem'][$key]);
		        $entity->setMedicineConfig($config);
		        $entity->setQuantity($qnt);
		        $entity->setMedicineStock($salesItem->getMedicineStock());
		        if($salesItem->getMedicinePurchaseItem()){
			        $entity->setMedicinePurchaseItem($salesItem->getMedicinePurchaseItem());
		        }
		        $entity->setMedicineSalesItem($salesItem);
		        $price = empty($data['price'][$key])? $salesItem->getSalesPrice() : $data['price'][$key];
		        $entity->setSalesPrice($price);
		        $entity->setSubTotal($entity->getSalesPrice() * $entity->getQuantity());
		        $em->persist($entity);
		        $em->flush();
		        if(!empty($salesItem->getMedicinePurchaseItem())) {
			        $this->getDoctrine()->getRepository( 'MedicineBundle:MedicinePurchaseItem' )->updateRemovePurchaseItemQuantity( $salesItem->getMedicinePurchaseItem(), 'sales-return' );
		        }
		        $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($salesItem->getMedicineStock(), 'sales-return');

	        }
        }
        $this->get('session')->getFlashBag()->add(
            'success',"Data has been inserted successfully"
        );
        return $this->redirect($this->generateUrl('medicine_sales_return'));

    }


    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MedicineBundle:MedicineSalesReturn')->find($id);
	    $item = $entity->getMedicinePurchaseItem();
        $stock = $entity->getMedicineStock();
	    $this->get('session')->set('item', $item);
	    $this->get('session')->set('stock', $stock);
	    $em->remove($entity);
        $em->flush();
	    $item = $this->get('session')->get('item');
	    $stock = $this->get('session')->get('stock');
	    if(!empty($item)) {
		    $this->getDoctrine()->getRepository( 'MedicineBundle:MedicinePurchaseItem' )->updateRemovePurchaseItemQuantity( $item, 'sales-return' );
	    }
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($stock,'sales-return');
        $this->get('session')->getFlashBag()->add(
            'error',"Data has been deleted successfully"
        );
        return $this->redirect($this->generateUrl('medicine_sales_return'));
    }

    public function approveAction(MedicineSalesReturn $entity)
    {

    	$em = $this->getDoctrine()->getManager();
        if (!empty($entity) and $entity->getProcess() !='approved') {
            $em = $this->getDoctrine()->getManager();
	        $entity->setProcess('approved');
            $journal = $em->getRepository('AccountingBundle:AccountJournal')->insertMedicineAccountSalesReturn($entity);
	        $entity->setJournal($journal);
            $em->flush();
	        return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;

    }


}
