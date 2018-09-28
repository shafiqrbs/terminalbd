<?php

namespace Appstore\Bundle\ElectionBundle\Controller;

use Appstore\Bundle\ElectionBundle\Entity\ElectionCommittee;
use Appstore\Bundle\ElectionBundle\Entity\ElectionCommitteeMember;
use Appstore\Bundle\ElectionBundle\Form\CommitteeMemberType;
use Appstore\Bundle\ElectionBundle\Form\CommitteeType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Symfony\Component\HttpFoundation\Response;

/**
 * Committee controller.
 *
 */
class CommitteeController extends Controller
{

	/**
	 * Lists all Committee entities.
	 *
	 */
	public function indexAction()
	{
		$em = $this->getDoctrine()->getManager();
		$entity = new ElectionCommittee();
		$config = $this->getUser()->getGlobalOption()->getElectionConfig();
		$entities = $this->getDoctrine()->getRepository('ElectionBundle:ElectionCommittee')->findBy(array('electionConfig' => $config,'committeeType'=>'election'),array('created'=>'DESC'));
		return $this->render('ElectionBundle:Committee:index.html.twig', array(
			'entities' => $entities,
			'entity' => $entity,
		));
	}


	/**
	 * @Secure(roles="ROLE_ELECTION,ROLE_DOMAIN")
	 */

	public function newAction()
	{
		$em = $this->getDoctrine()->getManager();
		$entity = new ElectionCommittee();
		$config = $this->getUser()->getGlobalOption()->getElectionConfig();
		$entity->setElectionConfig($config);
		$em->persist($entity);
		$em->flush();
		return $this->redirect($this->generateUrl('election_committee_edit', array('id' => $entity->getId())));

	}



	/**
	 * Creates a new Committee entity.
	 *
	 */
	public function createAction(Request $request)
	{
		$entity = new ElectionCommittee();
		$config = $this->getUser()->getGlobalOption()->getElectionConfig();
		$form = $this->createCreateForm($entity);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$config = $this->getUser()->getGlobalOption()->getElectionConfig();
			$entity->setElectionConfig($config);
			$entity->setCommitteeType('election');
			$em->persist($entity);
			$em->flush();
			$this->get('session')->getFlashBag()->add(
				'success',"Data has been inserted successfully"
			);
			return $this->redirect($this->generateUrl('election_committee', array('id' => $entity->getId())));
		}

		return $this->render('ElectionBundle:Committee:index.html.twig', array(
			'entity' => $entity,
			'form'   => $form->createView(),
		));
	}

	/**
	 * Creates a form to create a Committee entity.
	 *
	 * @param ElectionCommittee $entity The entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createCreateForm(ElectionCommittee $entity)
	{
		$config = $this->getUser()->getGlobalOption()->getElectionConfig();
		$location = $this->getDoctrine()->getRepository('ElectionBundle:ElectionLocation');
		$form = $this->createForm(new CommitteeType($config,$location), $entity, array(
			'action' => $this->generateUrl('election_committee_create'),
			'method' => 'POST',
			'attr' => array(
				'class' => 'horizontal-form',
				'novalidate' => 'novalidate',
			)
		));
		return $form;
	}

	private function createCommitteeForm(ElectionCommitteeMember $entity)
	{
		$config = $this->getUser()->getGlobalOption()->getElectionConfig();
		$form = $this->createForm(new CommitteeMemberType($config), $entity, array(
			'action' => $this->generateUrl('election_committee_member_create'),
			'method' => 'POST',
			'attr' => array(
				'class' => 'horizontal-form',
				'novalidate' => 'novalidate',
			)
		));
		return $form;
	}

	/**
	 * Displays a form to edit an existing Committee entity.
	 *
	 */
	public function editAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		$config = $this->getUser()->getGlobalOption()->getElectionConfig();
		$entity = $em->getRepository('ElectionBundle:ElectionCommittee')->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find Committee entity.');
		}

		$editForm = $this->createEditForm($entity);
		return $this->render('ElectionBundle:Committee:new.html.twig', array(
			'entity'      => $entity,
			'form'   => $editForm->createView(),
		));
	}

	/**
	 * Creates a form to edit a Committee entity.
	 *
	 * @param Committee $entity The entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createEditForm(ElectionCommittee $entity)
	{
		$config = $this->getUser()->getGlobalOption()->getElectionConfig();
		$location = $this->getDoctrine()->getRepository('ElectionBundle:ElectionLocation');

		$form = $this->createForm(new CommitteeType($config,$location), $entity, array(
			'action' => $this->generateUrl('election_committee_update', array('id' => $entity->getId())),
			'method' => 'PUT',
			'attr' => array(
				'class' => 'horizontal-form',
				'novalidate' => 'novalidate',
			)
		));
		return $form;
	}
	/**
	 * Edits an existing Committee entity.
	 *
	 */
	public function updateAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getManager();
		$config = $this->getUser()->getGlobalOption()->getElectionConfig();
		$entities = $this->getDoctrine()->getRepository('ElectionBundle:ElectionCommittee')->findBy(array('electionConfig' => $config),array('particularType'=>'ASC'));

		$entity = $em->getRepository('ElectionBundle:ElectionCommittee')->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find Committee entity.');
		}

		$editForm = $this->createEditForm($entity);
		$editForm->handleRequest($request);

		if ($editForm->isValid()) {
			$em->flush();
			$this->get('session')->getFlashBag()->add(
				'success',"Data has been changed successfully"
			);
			return $this->redirect($this->generateUrl('election_committee'));
		}

		return $this->render('ElectionBundle:Committee:index.html.twig', array(
			'entities'      => $entities,
			'entity'      => $entity,
			'form'   => $editForm->createView(),
		));
	}

	public function returnResultData(ElectionCommittee $entity, $msg=''){

		$invoiceParticulars = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->getSalesItems($entity);

		$subTotal = $entity->getSubTotal() > 0 ? $entity->getSubTotal() : 0;
		$netTotal = $entity->getTotal() > 0 ? $entity->getTotal() : 0;
		$payment = $entity->getPayment() > 0 ? $entity->getPayment() : 0;
		$vat = $entity->getVat() > 0 ? $entity->getVat() : 0;
		$due = $entity->getDue() > 0 ? $entity->getDue() : 0;
		$discount = $entity->getDiscount() > 0 ? $entity->getDiscount() : 0;
		$data = array(
			'subTotal' => $subTotal,
			'netTotal' => $netTotal,
			'payment' => $payment ,
			'due' => $due,
			'vat' => $vat,
			'discount' => $discount,
			'invoiceParticulars' => $invoiceParticulars ,
			'msg' => $msg ,
			'success' => 'success'
		);

		return $data;

	}


	public function addMemberAction(Request $request)
	{

		$em = $this->getDoctrine()->getManager();
		$data = $request->request->all();
		$committeeId = $data['committeeId'];
		$memberId = $data['member'];
		$designationId = $data['designation'];
		$committee = $this->getDoctrine()->getRepository('ElectionBundle:ElectionCommittee')->find($committeeId);
		$member = $this->getDoctrine()->getRepository('ElectionBundle:ElectionMember')->find($memberId);
		$designation = $this->getDoctrine()->getRepository('ElectionBundle:ElectionParticular')->find($designationId);
		$entity = new ElectionCommitteeMember();
		$entity->setCommittee($committee);
		$entity->setMember($member);
		$entity->setDesignation($designation);
		$em->persist($entity);
		$em->flush();
		$result = $this->returnResultData($entity);
		return new Response(json_encode($result));
		exit;

	}

	/**
	 * Deletes a Committee entity.
	 *
	 */
	public function deleteAction($id)
	{

		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('ElectionBundle:ElectionCommittee')->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find Committee entity.');
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

		return $this->redirect($this->generateUrl('election_committee'));
	}


	/**
	 * Status a Page entity.
	 *
	 */
	public function statusAction(Request $request, $id)
	{

		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('ElectionBundle:ElectionCommittee')->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find District entity.');
		}

		$status = $entity->isStatus();
		if($status == 1){
			$entity->setStatus(false);
		} else{
			$entity->setStatus(true);
		}
		$em->flush();
		$this->get('session')->getFlashBag()->add(
			'success',"Status has been changed successfully"
		);
		return $this->redirect($this->generateUrl('election_committee'));
	}

	public function autoSearchAction(Request $request)
	{
		$item = $_REQUEST['q'];
		if ($item) {
			$inventory = $this->getUser()->getGlobalOption()->getElectionConfig();
			$item = $this->getDoctrine()->getRepository('ElectionBundle:ElectionCommittee')->searchAutoComplete($item,$inventory);
		}
		return new JsonResponse($item);
	}

	public function searchCommitteeNameAction($vendor)
	{
		return new JsonResponse(array(
			'id'=>$vendor,
			'text'=>$vendor
		));
	}

	public function memberDeleteAction(ElectionCommitteeMember $entity)
	{
		$config = $this->getUser()->getGlobalOption()->getElectionConfig();
		if($entity->getCommittee()->getElectionConfig()->getId() == $config->getId() ){
			$em = $this->getDoctrine()->getManager();
			$em->remove($entity);
			$em->flush();
			return new Response('valid');
		}else{
			return new Response('in-valid');
		}

	}



}
