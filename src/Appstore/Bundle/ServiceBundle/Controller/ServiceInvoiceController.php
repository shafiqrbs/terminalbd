<?php

namespace Appstore\Bundle\ServiceBundle\Controller;

use Appstore\Bundle\AssetsBundle\Entity\Product;
use Appstore\Bundle\InventoryBundle\Entity\SalesItem;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\ServiceBundle\Entity\ServiceInvoice;
use Appstore\Bundle\ServiceBundle\Form\ServiceInvoiceType;

/**
 * ServiceInvoice controller.
 *
 */
class ServiceInvoiceController extends Controller
{

	/**
	 * Lists all ServiceInvoice entities.
	 *
	 */
	public function indexAction()
	{
		$em = $this->getDoctrine()->getManager();
		$entities = $em->getRepository( 'ServiceBundle:ServiceInvoice' )->findAll();

		return $this->render('ServiceBundle:ServiceInvoice:index.html.twig', array(
			'entities' => $entities,
		));
	}
	/**
	 * Creates a new ServiceInvoice entity.
	 *
	 */
	public function createAction(Request $request)
	{
		$entity = new ServiceInvoice();
		$form = $this->createCreateForm($entity);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();
			$this->get('session')->getFlashBag()->add(
				'success',"Data has been added successfully"
			);
			return $this->redirect($this->generateUrl('serviceinvoice'));
		}

		return $this->render('ServiceBundle:ServiceInvoice:new.html.twig', array(
			'entity' => $entity,
			'form'   => $form->createView(),
		));
	}

	/**
	 * Creates a form to create a ServiceInvoice entity.
	 *
	 * @param ServiceInvoice $entity The entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createCreateForm(ServiceInvoice $entity)
	{
		$form = $this->createForm(new ServiceInvoiceType(), $entity, array(
			'action' => $this->generateUrl('serviceinvoice_create'),
			'method' => 'POST',
			'attr' => array(
				'class' => 'horizontal-form',
				'novalidate' => 'novalidate',
			)
		));
		return $form;
	}

	/**
	 * Displays a form to create a new ServiceInvoice entity.
	 *
	 */
	public function newAction()
	{
		$entity = new ServiceInvoice();
		$form   = $this->createCreateForm($entity);

		return $this->render('ServiceBundle:ServiceInvoice:new.html.twig', array(
			'entity' => $entity,
			'form'   => $form->createView(),
		));
	}

	/**
	 * Displays a form to create a new ServiceInvoice entity.
	 *
	 */
	public function generateAction(Product $item)
	{

		$em = $this->getDoctrine()->getManager();

		$entity = new ServiceInvoice();
		$entity->setProduct($item);
		$entity->setVendor($item->getVendor());
		$entity->setBranch($item->getBranch());
		$entity->setAssignBy($this->getUser());
		$entity->setItemIdentifier($item->getSerialNo());
		$entity->setAssuranceType($item->getAssuranceType());
		$em->persist($entity);
		$em->flush();
		return $this->redirect($this->generateUrl('serviceinvoice_edit',array('id' => $entity->getId())));
	}

	/**
	 * Finds and displays a ServiceInvoice entity.
	 *
	 */
	public function showAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository( 'ServiceBundle:ServiceInvoice' )->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find ServiceInvoice entity.');
		}

		return $this->render('ServiceBundle:ServiceInvoice:show.html.twig', array(
			'entity'      => $entity,
		));
	}

	/**
	 * Displays a form to edit an existing ServiceInvoice entity.
	 *
	 */
	public function editAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository( 'ServiceBundle:ServiceInvoice' )->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find ServiceInvoice entity.');
		}

		$editForm = $this->createEditForm($entity);

		return $this->render('ServiceBundle:ServiceInvoice:new.html.twig', array(
			'entity'      => $entity,
			'form'   => $editForm->createView(),
		));
	}

	/**
	 * Creates a form to edit a ServiceInvoice entity.
	 *
	 * @param ServiceInvoice $entity The entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createEditForm(ServiceInvoice $entity)
	{
		$form = $this->createForm(new ServiceInvoiceType(), $entity, array(
			'action' => $this->generateUrl('serviceinvoice_update', array('id' => $entity->getId())),
			'method' => 'PUT',
			'attr' => array(
				'class' => 'horizontal-form',
				'novalidate' => 'novalidate',
			)
		));
		return $form;
	}
	/**
	 * Edits an existing ServiceInvoice entity.
	 *
	 */
	public function updateAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository( 'ServiceBundle:ServiceInvoice' )->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find ServiceInvoice entity.');
		}

		$editForm = $this->createEditForm($entity);
		$editForm->handleRequest($request);

		if ($editForm->isValid()) {
			$em->flush();
			$this->get('session')->getFlashBag()->add(
				'success',"Data has been updated successfully"
			);
			return $this->redirect($this->generateUrl('serviceinvoice'));
		}

		return $this->render('ServiceBundle:ServiceInvoice:new.html.twig', array(
			'entity'      => $entity,
			'form'   => $editForm->createView(),
		));
	}

	/**
	 * Deletes a ServiceInvoice entity.
	 *
	 */
	public function deleteAction(ServiceInvoice $entity)
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

		return $this->redirect($this->generateUrl('serviceinvoice'));
	}

	public function autoSearchAction(Request $request)
	{
		$item = $_REQUEST['q'];
		if ($item) {
			$inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
			$item = $this->getDoctrine()->getRepository( 'ServiceBundle:ServiceInvoice' )->searchAutoComplete($item,$inventory);
		}
		return new JsonResponse($item);
	}

	public function searchServiceInvoiceNameAction($brand)
	{
		return new JsonResponse(array(
			'id'=> $brand,
			'text'=> $brand
		));
	}
}
