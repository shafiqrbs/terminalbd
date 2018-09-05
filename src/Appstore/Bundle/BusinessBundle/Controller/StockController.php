<?php

namespace Appstore\Bundle\BusinessBundle\Controller;
use Appstore\Bundle\BusinessBundle\Entity\BusinessConfig;
use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Form\StockType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * StockController controller.
 *
 */
class StockController extends Controller
{

    public function paginate($entities)
    {

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        $pagination->setTemplate('SettingToolBundle:Widget:pagination.html.twig');
        return $pagination;
    }

    /**
     * Lists all Particular entities.
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->findWithSearch($config,$data);
        $pagination = $this->paginate($entities);
        $type = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticularType')->findBy(array('status'=>1));
        $category = $this->getDoctrine()->getRepository('BusinessBundle:Category')->findBy(array('status'=>1));
        return $this->render('BusinessBundle:Stock:index.html.twig', array(
            'pagination' => $pagination,
            'types' => $type,
            'categories' => $category,
            'config' => $config,
            'searchForm' => $data,
        ));

    }

    /**
     * Displays a form to create a new Vendor entity.
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     */

    public function newAction()
    {
        $entity = new BusinessParticular();
	    $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
	    $stockFormat = $config->getStockFormat();
        $form   = $this->createCreateForm($entity);
        return $this->render('BusinessBundle:Stock:new.html.twig', array(
            'entity' => $entity,
            'stockFormat' => $stockFormat,
            'form'   => $form->createView()
        ));
    }


    /**
     * Creates a new BusinessParticular entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $config BusinessConfig */

        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entity = new BusinessParticular();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
	    if(!empty($entity->getWearHouse()) and !empty($entity->getWearHouse()->getWearHouseCode())) {
		    $name = $entity->getWearHouse()->getShortCode() . '-' . $entity->getName();
		    $checkName = $this->getDoctrine()->getRepository( 'BusinessBundle:BusinessParticular' )->findOneBy( array(
			    'businessConfig' => $config,
			    'name'           => $name
		    ) );
	    }else{
		    $name = $entity->getName();
		    $checkName = $this->getDoctrine()->getRepository( 'BusinessBundle:BusinessParticular' )->findOneBy( array(
			    'businessConfig' => $config,
			    'name'           => $name
		    ) );
	    }
        if ($form->isValid() and empty($checkName)) {
            $em = $this->getDoctrine()->getManager();
            $entity->setBusinessConfig($config);
            if($entity->getBusinessParticularType() == 'production' ){
               if(!empty($config->getProductionType())){
                   $entity->setProductionType($config->getProductionType());
               }
            }
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('business_stock_new'));
        }
	    $stockFormat = $config->getStockFormat();
        $this->get('session')->getFlashBag()->add(
            'error',"Required field does not input"
        );
        return $this->render('BusinessBundle:Stock:new.html.twig', array(
            'entity'        => $entity,
            'stockFormat'   => $stockFormat,
            'form'          => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Particular entity.
     *
     * @param Particular $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BusinessParticular $entity)
    {

        $option = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new StockType($option), $entity, array(
            'action' => $this->generateUrl('business_stock_create', array('id' => $entity->getId())),
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
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BusinessBundle:BusinessParticular')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
	    $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
	    $stockFormat = $config->getStockFormat();
        $editForm = $this->createEditForm($entity);
        return $this->render('BusinessBundle:Stock:new.html.twig', array(
            'entity'            => $entity,
            'stockFormat'       => $stockFormat,
            'form'              => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Particular entity.
     *
     * @param Particular $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(BusinessParticular $entity)
    {
        $option = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new StockType($option), $entity, array(
            'action' => $this->generateUrl('business_stock_update', array('id' => $entity->getId())),
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
        $entity = $em->getRepository('BusinessBundle:BusinessParticular')->find($id);
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
            return $this->redirect($this->generateUrl('business_stock'));
        }
        return $this->render('BusinessBundle:Stock:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Particular entity.
     * @Secure(roles="ROLE_BUSINESS_STOCK,ROLE_DOMAIN");
     */

    public function deleteAction(BusinessParticular $entity)
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
        return $this->redirect($this->generateUrl('business_stock'));
    }

   
    /**
     * Status a Page entity.
     *
     */

    public function statusAction(BusinessParticular $entity)
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
        return $this->redirect($this->generateUrl('business_stock'));
    }

    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BusinessBundle:BusinessParticular')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find particular entity.');
        }
	    if('openingQuantity' == $data['name']) {
		    $setField = 'set' . $data['name'];
		    $quantity = abs($data['value']);
		    $entity->$setField($quantity);
		    $remainingQuantity = $entity->getRemainingQuantity() + $quantity;
		    $entity->setRemainingQuantity($remainingQuantity);
	    }else{
		    $setField = 'set' . $data['name'];
		    $entity->$setField(abs($data['value']));
	    }
        $em->flush();
        exit;

    }

    public function production(BusinessParticular $particular)
    {

    }

    public function transfer(BusinessParticular $particular)
    {

    }

	public function autoSearchAction(Request $request)
	{
		$item = $_REQUEST['q'];
		if ($item) {
			$inventory = $this->getUser()->getGlobalOption()->getBusinessConfig();
			$item = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->searchAutoComplete($inventory,$item);
		}
		return new JsonResponse($item);
	}

	public function searchNameAction($stock)
	{
		return new JsonResponse(array(
			'id'=> $stock,
			'text'=> $stock
		));
	}

	public function stockQuantityUpdateAction()
	{
		set_time_limit(0);
		ignore_user_abort(true);
		$em = $this->getDoctrine()->getManager();
		$config = $this->getUser()->getGlobalOption()->getMedicineConfig();
		$items = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->findBy(array('medicineConfig'=>$config));

		/* @var BusinessParticular $item */

		foreach ($items as $item){
			$this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($item,'');
			$this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($item,'sales');
			$this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($item,'sales-return');
			$this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($item,'purchase-return');
			$this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($item,'damage');
		}
		return $this->redirect($this->generateUrl('medicine_stock'));
	}


}
