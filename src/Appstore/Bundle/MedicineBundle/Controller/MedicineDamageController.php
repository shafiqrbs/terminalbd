<?php

namespace Appstore\Bundle\MedicineBundle\Controller;

use Appstore\Bundle\MedicineBundle\Entity\MedicineDamage;
use Appstore\Bundle\MedicineBundle\Form\MedicineDamageType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Damage controller.
 *
 */
class MedicineDamageController extends Controller
{

    /**
     * Lists all Damage entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new MedicineDamage();
        $form = $this->createCreateForm($entity);
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineDamage')->findBy(array('medicineConfig' => $config),array('created'=>'ASC'));
        return $this->render('MedicineBundle:Damage:index.html.twig', array(
            'entities' => $entities,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    public function newAction(Request $request){

        $entity = new MedicineDamage();
        $form = $this->createCreateForm($entity);
        return $this->render('MedicineBundle:Damage:new.html.twig', array(
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
        $entity = new MedicineDamage();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $data = $request->request->all();
        $stock = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->find($data['damage']['medicineStock']);
      //  $purchaseItem = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->find($data['medicinePurchaseItem']);

        if ($form->isValid() and !empty($stock)) {

            $em = $this->getDoctrine()->getManager();
            $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
            $entity->setMedicineConfig($config);
            $entity->setMedicineStock($stock);
       //     $entity->setMedicinePurchaseItem($purchaseItem);
            $entity->setPurchasePrice($stock->getPurchasePrice());
            $entity->setSubTotal($stock->getPurchasePrice() * $entity->getQuantity());
            $em->persist($entity);
            $em->flush();
          //  $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->updateRemovePurchaseItemQuantity($purchaseItem,'damage');
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($stock,'damage');
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('medicine_damage', array('id' => $entity->getId())));
        }

        return $this->render('MedicineBundle:Damage:new.html.twig', array(
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
    private function createCreateForm(MedicineDamage $entity)
    {
        $form = $this->createForm(new MedicineDamageType(), $entity, array(
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
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineDamage')->findBy(array('medicineConfig' => $config),array('companyName'=>'ASC'));

        $entity = $em->getRepository('MedicineBundle:MedicineDamage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Damage entity.');
        }

        $editForm = $this->createEditForm($entity);


        return $this->render('MedicineBundle:Damage:index.html.twig', array(
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
    private function createEditForm(MedicineDamage $entity)
    {
        $form = $this->createForm(new MedicineDamageType(), $entity, array(
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
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineDamage')->findBy(array('medicineConfig' => $config),array('companyName'=>'ASC'));

        $entity = $em->getRepository('MedicineBundle:MedicineDamage')->find($id);

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
        return $this->render('MedicineBundle:Damage:index.html.twig', array(
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
        $entity = $em->getRepository('MedicineBundle:MedicineDamage')->find($id);
       // $purchaseItem = $entity->getMedicinePurchaseItem();
        $stock = $entity->getMedicineStock();
        $em->remove($entity);
        $em->flush();
      //  $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->updateRemovePurchaseItemQuantity($purchaseItem,'damage');
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($stock,'damage');
        $this->get('session')->getFlashBag()->add(
            'error',"Data has been deleted successfully"
        );
        return $this->redirect($this->generateUrl('medicine_damage'));
    }

	public function approvedAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		$config = $this->getUser()->getGlobalOption()->getMedicineConfig();
		$damage = $em->getRepository('MedicineBundle:MedicineDamage')->findOneBy(array('medicineConfig' => $config , 'id' => $id));
		if (!empty($damage) and ($damage->getProcess() == 'Created')) {
			$em = $this->getDoctrine()->getManager();
			$damage->setProcess('Approved');
			$em->flush();
			$em->getRepository('AccountingBundle:Transaction')->insertGlobalDamageTransaction($this->getUser()->getGlobalOption(),$damage);
			return new Response('success');
		} else {
			return new Response('failed');
		}
		exit;
	}


}
