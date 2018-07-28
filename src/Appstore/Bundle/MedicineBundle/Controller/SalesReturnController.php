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
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesReturn')->findBy(array('medicineConfig' => $config),array('created'=>'ASC'));
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
            $entity = new MedicineSalesReturn();
            $salesItem = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesItem')->find($data['salesItem'][$key]);
            $entity->setMedicineConfig($config);
            $entity->setAccountMode($data['accountMode']);
            $entity->setQuantity($qnt);
            $entity->setMedicineStock($salesItem->getMedicineStock());
            $entity->setMedicinePurchaseItem($salesItem->getMedicinePurchaseItem());
            $entity->setMedicineSalesItem($salesItem);
            $price = empty($data[$key])? $salesItem->getSalesPrice() : $data['salesPrice'][$key];
            $entity->setSalesPrice($price);
            $entity->setSubTotal($entity->getSalesPrice() * $entity->getQuantity());
            $em->persist($entity);
            $em->flush();
            $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->updateRemovePurchaseItemQuantity($salesItem->getMedicinePurchaseItem(), 'damage');
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($salesItem->getMedicineStock(), 'damage');
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
        $purchaseItem = $entity->getMedicinePurchaseItem();
        $stock = $entity->getMedicineStock();
        $em->remove($entity);
        $em->flush();
        $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->updateRemovePurchaseItemQuantity($purchaseItem,'sales-return');
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($stock,'sales-return');
        $this->get('session')->getFlashBag()->add(
            'error',"Data has been deleted successfully"
        );
        return $this->redirect($this->generateUrl('medicine_damage'));
    }

    public function approveAction(MedicineSalesReturn $entity)
    {

    	$em = $this->getDoctrine()->getManager();
        if (!empty($entity) and $entity->getProcess() !='approved') {
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess('approved');
            $em->flush();
            if($entity->getAccountMode() == 'adjustment'){
	            $account = $em->getRepository('AccountingBundle:AccountSales')->insertMedicineAccountSalesReturn($entity);
	            $em->getRepository('AccountingBundle:Transaction')->salesReturnGlobalPayableTransaction($account);
            }elseif($entity->getAccountMode() == 'cash-return'){
	            $em->getRepository('AccountingBundle:AccountJournal')->insertMedicineAccountSalesReturn($entity);
	        }
            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;

    }


}
