<?php

namespace Appstore\Bundle\AssetsBundle\Controller;

use Appstore\Bundle\AssetsBundle\Entity\Product;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\AssetsBundle\Entity\DepreciationModel;
use Appstore\Bundle\AssetsBundle\Form\DepreciationModelType;

/**
 * DepreciationModel controller.
 *
 */
class DepreciationModelController extends Controller
{

	/**
	 * Lists all DepreciationModel entities.
	 *
	 */
	public function indexAction()
	{
		$depreciation = $this->getDoctrine()->getRepository('AssetsBundle:Depreciation')->find(1);
		$em = $this->getDoctrine()->getManager();
		$entities = $em->getRepository( 'AssetsBundle:DepreciationModel' )->findBy(array(),array( 'name' =>'asc'));
		return $this->render('AssetsBundle:DepreciationModel:index.html.twig', array(
			'depreciation' => $depreciation,
			'entities' => $entities,
		));
	}
	/**
	 * Creates a new DepreciationModel entity.
	 *
	 */
	public function createAction(Request $request)
	{
		$entity = new DepreciationModel();
		$form = $this->createCreateForm($entity);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();
			$this->get('session')->getFlashBag()->add(
				'success',"Data has been added successfully"
			);
			return $this->redirect($this->generateUrl('assets_model'));
		}
		$depreciation = $this->getDoctrine()->getRepository('AssetsBundle:Depreciation')->find(1);

		return $this->render('AssetsBundle:DepreciationModel:new.html.twig', array(
			'entity' => $entity,
			'depreciation' => $depreciation,
			'form'   => $form->createView(),
		));
	}

	/**
	 * Creates a form to create a DepreciationModel entity.
	 *
	 * @param DepreciationModel $entity The entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createCreateForm(DepreciationModel $entity)
	{
		$inventory = $this->getDoctrine()->getRepository('InventoryBundle:InventoryConfig')->find(4);
		$depreciation = $this->getDoctrine()->getRepository('AssetsBundle:Depreciation')->find(1);
		$em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
		$form = $this->createForm(new DepreciationModelType($em,$inventory,$depreciation), $entity, array(
			'action' => $this->generateUrl('assets_model_create'),
			'method' => 'POST',
			'attr' => array(
				'class' => 'horizontal-form',
				'novalidate' => 'novalidate',
			)
		));
		return $form;
	}

	/**
	 * Displays a form to create a new DepreciationModel entity.
	 *
	 */
	public function newAction()
	{
		$entity = new DepreciationModel();
		$form   = $this->createCreateForm($entity);
		$depreciation = $this->getDoctrine()->getRepository('AssetsBundle:Depreciation')->find(1);

		return $this->render('AssetsBundle:DepreciationModel:new.html.twig', array(
			'entity' => $entity,
			'depreciation' => $depreciation,
			'form'   => $form->createView(),
		));
	}

	/**
	 * Finds and displays a DepreciationModel entity.
	 *
	 */
	public function depreciationAction(DepreciationModel $entity)
	{
		$em = $this->getDoctrine()->getManager();
		set_time_limit(0);
		ignore_user_abort(true);

		$data = array();
		if(!empty($entity)){
			if(!empty($entity->getItem())){
				$data[] = array('item' => $entity->getItem()->getId());
			}
			if(!empty($entity->getCategory())){
				$data[] = array('category' => $entity->getCategory()->getId());
			}
		}
		$products = $this->getDoctrine()->getRepository('AssetsBundle:Product')->findWithSearch($data);

		/* @var $product Product */

		if(!empty($products->getQuery()->getResult())){

			foreach ($products->getQuery()->getResult() as $product):
				if($product->isCustomDepreciation() != 1){
					$product->setDepreciation($entity);
					$em->persist($product);
					$em->flush();
				}
			endforeach;
			$this->get('session')->getFlashBag()->add(
				'success',"Depreciation rate has been added successfully"
			);
		}
		return $this->redirect($this->generateUrl('assets_model'));
	}

	/**
	 * Displays a form to edit an existing DepreciationModel entity.
	 *
	 */



	public function editAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository( 'AssetsBundle:DepreciationModel' )->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find DepreciationModel entity.');
		}

		$editForm = $this->createEditForm($entity);
		$depreciation = $this->getDoctrine()->getRepository('AssetsBundle:Depreciation')->find(1);

		return $this->render('AssetsBundle:DepreciationModel:new.html.twig', array(
			'entity'      => $entity,
			'depreciation'      => $depreciation,
			'form'   => $editForm->createView(),
		));
	}

	/**
	 * Creates a form to edit a DepreciationModel entity.
	 *
	 * @param DepreciationModel $entity The entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createEditForm(DepreciationModel $entity)
	{
		$inventory = $this->getDoctrine()->getRepository('InventoryBundle:InventoryConfig')->find(4);
		$em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
		$depreciation = $this->getDoctrine()->getRepository('AssetsBundle:Depreciation')->find(1);

		$form = $this->createForm(new DepreciationModelType($em,$inventory,$depreciation), $entity, array(
			'action' => $this->generateUrl('assets_model_update', array('id' => $entity->getId())),
			'method' => 'PUT',
			'attr' => array(
				'class' => 'horizontal-form',
				'novalidate' => 'novalidate',
			)
		));
		return $form;
	}
	/**
	 * Edits an existing DepreciationModel entity.
	 *
	 */
	public function updateAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository( 'AssetsBundle:DepreciationModel' )->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find DepreciationModel entity.');
		}

		$editForm = $this->createEditForm($entity);
		$editForm->handleRequest($request);

		if ($editForm->isValid()) {
			$em->flush();
			$this->get('session')->getFlashBag()->add(
				'success',"Data has been updated successfully"
			);
			return $this->redirect($this->generateUrl('assets_model'));
		}
		$depreciation = $this->getDoctrine()->getRepository('AssetsBundle:Depreciation')->find(1);

		return $this->render('AssetsBundle:DepreciationModel:new.html.twig', array(
			'entity'      => $entity,
			'depreciation'      => $depreciation,
			'form'   => $editForm->createView(),
		));
	}

	/**
	 * Deletes a DepreciationModel entity.
	 *
	 */
	public function deleteAction(DepreciationModel $entity)
	{
		$em = $this->getDoctrine()->getManager();
		if (!$entity) {
			throw $this->createNotFoundException('Unable to find Brand entity.');
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
		return $this->redirect($this->generateUrl('assets_model'));
	}


	public function generateAction(DepreciationModel $entity)
	{
		$em = $this->getDoctrine()->getManager();
		set_time_limit(0);
		ignore_user_abort(true);

		$depreciation = $this->getDoctrine()->getRepository('AssetsBundle:Depreciation')->find(1);
		$data = array('depreciation' => $entity->getId());
		$products = $this->getDoctrine()->getRepository('AssetsBundle:Product')->findWithSearch($data);

		/* @var $product Product */

		if(!empty($products->getQuery()->getResult())){

			foreach ($products->getQuery()->getResult() as $product):

			if($depreciation->getPolicy() == 'straight-line'){

                if($product->getStraightLineValue() > 0){
                    $product->getStraightLineValue();
	                $currentBookValue = ($product->getBookValue() - $product->getStraightLineValue());
					$product->setBookValue($currentBookValue);
					$depValue = ($product->getDepreciationValue() + $product->getStraightLineValue());
					$product->setDepreciationValue($depValue);
					$em->persist($product);
					$em->flush($product);
					$this->getDoctrine()->getRepository('AssetsBundle:ProductLedger')->insertProductDepreciation($product,$product->getStraightLineValue());

				}else{

					$this->generateStraightLineValue($entity,$product);
				}

			}else{

				if($product->getReducingBalancePercentage()){
					$depValue = ($product->getBookValue() * $product->getReducingBalancePercentage())/100;
					$bookValue = ($product->getBookValue() - $depValue);
					$product->setBookValue($bookValue);
					$depValue = ($product->getDepreciationValue() + $depValue);
					$product->setDepreciationValue($depValue);
					$em->persist($product);
                    $em->flush($product);
					$this->getDoctrine()->getRepository('AssetsBundle:ProductLedger')->insertProductDepreciation($product,$depValue);

				}else{
					$this->generateReducingBalance($entity,$product);
				}
			}

			endforeach;
			$this->get('session')->getFlashBag()->add(
				'success',"Depreciation has been generated successfully"
			);
		}
		return $this->redirect($this->generateUrl('assets_model'));
	}

	public function generateStraightLineValue(DepreciationModel $model ,Product $product)
	{
		$depreciation = $this->getDoctrine()->getRepository('AssetsBundle:Depreciation')->find(1);
		$straightLine = (($product->getPurchasePrice() - $product->getSalvageValue()) / $model->getDepreciationYear());
		$straightValue = 0;
		if( $depreciation->getDepreciationPulse() == 'monthly'){
			$month = ($model->getDepreciationYear()*12);
			$straightValue = ($straightLine/$month);
		}elseif( $depreciation->getDepreciationPulse() == 'quarterly'){
			$month = ($model->getDepreciationYear()*4);
			$straightValue = ($straightLine/$month);
		}elseif( $depreciation->getDepreciationPulse() == 'half-year'){
			$month = ($model->getDepreciationYear()*2);
			$straightValue = ($straightLine/$month);
		}else{
			$straightValue =$straightLine;
		}
		$em = $this->getDoctrine()->getManager();
		$bookValue = ($product->getPurchasePrice() - ($product->getSalvageValue() + $straightValue));
		$product->setBookValue($bookValue);
		$product->setDepreciationValue($straightValue);
		$product->setStraightLineValue($straightValue);
		$straightPercentage = (($straightValue * 100)/$product->getPurchasePrice());
		$product->setStraightLinePercentage($straightPercentage);
		$product->setDepreciationRate($straightPercentage);
		$em->persist($product);
		$em->flush();
		$this->getDoctrine()->getRepository('AssetsBundle:ProductLedger')->insertProductDepreciation($product,$straightValue);

	}

	public function generateReducingBalance(DepreciationModel $model ,Product $product)
	{
		$depreciation = $this->getDoctrine()->getRepository('AssetsBundle:Depreciation')->find(1);
		$straightValue = 0;
		if( $depreciation->getDepreciationPulse() == 'monthly'){
			$rate = ($model->getRate()/12);
		}elseif( $depreciation->getDepreciationPulse() == 'quarterly'){
			$rate = ($model->getRate()/4);
		}elseif( $depreciation->getDepreciationPulse() == 'half-year'){
			$rate = ($model->getRate()/2);
		}else{
			$rate = $model->getRate();
		}
		$em = $this->getDoctrine()->getManager();

		$depValue = ((($product->getPurchasePrice() - $product->getSalvageValue()) * $rate)/100);
		$bookValue = ($product->getPurchasePrice() - ($product->getSalvageValue() + $depValue));

		$product->setBookValue($bookValue);
		$product->setDepreciationValue($depValue);
		$product->setReducingBalancePercentage($rate);
		$product->setDepreciationRate($rate);
		$em->persist($product);
		$em->flush();
		$this->getDoctrine()->getRepository('AssetsBundle:ProductLedger')->insertProductDepreciation($product,$depValue);

	}



}
