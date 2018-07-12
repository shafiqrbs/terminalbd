<?php

namespace Appstore\Bundle\BusinessBundle\Controller;

use Appstore\Bundle\BusinessBundle\Entity\BusinessDamage;
use Appstore\Bundle\BusinessBundle\Form\BusinessDamageType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Damage controller.
 *
 */
class BusinessDamageController extends Controller
{

    /**
     * Lists all Damage entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new BusinessDamage();
        $form = $this->createCreateForm($entity);
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessDamage')->findBy(array('medicineConfig' => $config),array('created'=>'ASC'));
        return $this->render('BusinessBundle:Damage:index.html.twig', array(
            'entities' => $entities,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    public function newAction(Request $request){

        $entity = new BusinessDamage();
        $form = $this->createCreateForm($entity);
        return $this->render('BusinessBundle:Damage:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Damage entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new BusinessDamage();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $data = $request->request->all();
        $stock = $this->getDoctrine()->getRepository('BusinessBundle:BusinessStock')->find($data['damage']['medicineStock']);
        $purchaseItem = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchaseItem')->find($data['medicinePurchaseItem']);

        if ($form->isValid() and !empty($purchaseItem)) {

            $em = $this->getDoctrine()->getManager();
            $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
            $entity->setBusinessConfig($config);
            $entity->setBusinessStock($stock);
            $entity->setBusinessPurchaseItem($purchaseItem);
            $entity->setPurchasePrice($purchaseItem->getPurchasePrice());
            $entity->setSubTotal($purchaseItem->getPurchasePrice() * $entity->getQuantity());
            $em->persist($entity);
            $em->flush();
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchaseItem')->updateRemovePurchaseItemQuantity($purchaseItem,'damage');
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessStock')->updateRemovePurchaseQuantity($stock,'damage');
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('medicine_damage', array('id' => $entity->getId())));
        }

        return $this->render('BusinessBundle:Damage:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Damage entity.
     *
     * @param Damage $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BusinessDamage $entity)
    {
        $form = $this->createForm(new BusinessDamageType(), $entity, array(
            'action' => $this->generateUrl('medicine_damage_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to edit an existing Damage entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessDamage')->findBy(array('medicineConfig' => $config),array('companyName'=>'ASC'));

        $entity = $em->getRepository('BusinessBundle:BusinessDamage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Damage entity.');
        }

        $editForm = $this->createEditForm($entity);


        return $this->render('BusinessBundle:Damage:index.html.twig', array(
            'entities'      => $entities,
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Damage entity.
    *
    * @param Damage $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(BusinessDamage $entity)
    {
        $form = $this->createForm(new BusinessDamageType(), $entity, array(
            'action' => $this->generateUrl('medicine_damage_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing Damage entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessDamage')->findBy(array('medicineConfig' => $config),array('companyName'=>'ASC'));

        $entity = $em->getRepository('BusinessBundle:BusinessDamage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Damage entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been changed successfully"
            );
            return $this->redirect($this->generateUrl('medicine_damage'));
        }
        return $this->render('BusinessBundle:Damage:index.html.twig', array(
            'entities'      => $entities,
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a Damage entity.
     *
     */
    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BusinessBundle:BusinessDamage')->find($id);
        $purchaseItem = $entity->getBusinessPurchaseItem();
        $stock = $entity->getBusinessStock();
        $em->remove($entity);
        $em->flush();
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchaseItem')->updateRemovePurchaseItemQuantity($purchaseItem,'damage');
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessStock')->updateRemovePurchaseQuantity($stock,'damage');
        $this->get('session')->getFlashBag()->add(
            'error',"Data has been deleted successfully"
        );
        return $this->redirect($this->generateUrl('medicine_damage'));
    }

}
