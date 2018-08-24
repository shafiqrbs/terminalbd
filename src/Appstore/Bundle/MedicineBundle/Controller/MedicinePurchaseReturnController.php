<?php

namespace Appstore\Bundle\MedicineBundle\Controller;

use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseReturn;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseReturnItem;
use Appstore\Bundle\MedicineBundle\Form\PurchaseReturnItemType;
use Appstore\Bundle\MedicineBundle\Form\PurchaseReturnType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * PurchaseReturn controller.
 *
 */
class MedicinePurchaseReturnController extends Controller
{

    /**
     * Lists all PurchaseReturn entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new MedicinePurchaseReturn();
        $form = $this->createCreateForm($entity);
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseReturn')->findBy(array('medicineConfig' => $config),array('created'=>'ASC'));
        return $this->render('MedicineBundle:PurchaseReturn:index.html.twig', array(
            'entities' => $entities,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    public function newAction(){

        $entity = new MedicinePurchaseReturn();
        $form = $this->createCreateForm($entity);
        return $this->render('MedicineBundle:PurchaseReturn:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }


    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MedicineBundle:MedicinePurchaseReturn')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        return $this->render('MedicineBundle:PurchaseReturn:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Creates a new PurchaseReturn entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new MedicinePurchaseReturn();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
            $entity->setMedicineConfig($config);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('medicine_purchase_return_edit', array('id' => $entity->getId())));
        }

        return $this->render('MedicineBundle:PurchaseReturn:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a PurchaseReturn entity.
     *
     * @param PurchaseReturn $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MedicinePurchaseReturn $entity)
    {
        $global = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new PurchaseReturnType($global), $entity, array(
            'action' => $this->generateUrl('medicine_purchase_return_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to edit an existing PurchaseReturn entity.
     *
     */
    public function editAction(MedicinePurchaseReturn $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $purchaseItem = new MedicinePurchaseReturnItem();
        $form = $this->createEditForm($entity,$purchaseItem);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseReturn entity.');
        }
        return $this->render('MedicineBundle:PurchaseReturn:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to edit a PurchaseReturn entity.
    *
    * @param PurchaseReturn $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(MedicinePurchaseReturn $entity,MedicinePurchaseReturnItem $purchaseItem)
    {
        $form = $this->createForm(new PurchaseReturnItemType(), $purchaseItem, array(
            'action' => $this->generateUrl('medicine_purchase_return_update', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing PurchaseReturn entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MedicineBundle:MedicinePurchaseReturn')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseReturn entity.');
        }
        $data = $request->request->all();
        $purchaseReturnItem = new MedicinePurchaseReturnItem();
        $form = $this->createEditForm($entity,$purchaseReturnItem);
        $form->handleRequest($request);
        if ($form->isValid()) {

            $purchaseReturnItem->setMedicinePurchaseReturn($entity);

            $stock = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->find($data['medicineStock']);
            $purchaseReturnItem->setMedicineStock($stock);
          //  $purchaseItem = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->find($data['medicinePurchaseItem']);
           // $purchaseReturnItem->setMedicinePurchaseItem($purchaseItem);
            $purchaseReturnItem->setPurchasePrice($stock->getPurchasePrice());
            $purchaseReturnItem->setSubTotal($purchaseReturnItem->getPurchasePrice() * $purchaseReturnItem->getQuantity());
            $em->persist($purchaseReturnItem);
            $em->flush();
           // $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->updateRemovePurchaseItemQuantity($purchaseItem,'purchase-return');
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($stock,'purchase-return');
            $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseReturn')->updatePurchaseReturnTotalPrice($entity);
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('medicine_purchase_return_edit',array('id'=>$entity->getId())));
        }
        return $this->render('MedicineBundle:PurchaseReturn:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $form->createView(),
        ));
    }
    /**
     * Deletes a PurchaseReturn entity.
     *
     */
    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MedicineBundle:MedicinePurchaseReturn')->find($id);
        $purchaseItem = $entity->getMedicinePurchaseItem();
        $stock = $entity->getMedicineStock();
        $em->remove($entity);
        $em->flush();
        $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->updateRemovePurchaseItemQuantity($purchaseItem,'purchase_return');
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($stock,'purchase_return');
        $this->get('session')->getFlashBag()->add(
            'error',"Data has been deleted successfully"
        );
        return $this->redirect($this->generateUrl('medicine_purchase_return'));
    }

    public function approveAction(MedicinePurchaseReturn $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!empty($entity)) {
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess('approved');
            $entity->setApprovedBy($this->getUser());
            $em->flush();
            $accountPurchase = $em->getRepository('AccountingBundle:AccountPurchase')->insertMedicineAccountPurchaseReturn($entity);
            $em->getRepository('AccountingBundle:Transaction')->purchaseReturnGlobalTransaction($accountPurchase);
            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;

    }

}
