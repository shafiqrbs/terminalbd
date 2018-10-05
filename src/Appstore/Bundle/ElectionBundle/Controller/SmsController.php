<?php

namespace Appstore\Bundle\ElectionBundle\Controller;

use Appstore\Bundle\ElectionBundle\Entity\ElectionMember;
use Appstore\Bundle\ElectionBundle\Entity\ElectionMemberFamily;
use Appstore\Bundle\ElectionBundle\Form\MemberType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Symfony\Component\HttpFoundation\Response;


/**
 * ElectionMember controller.
 *
 */
class SmsController extends Controller
{
	/**
	 * Finds and displays a ElectionMember entity.
	 *
	 */
	public function memberAction(Request $request , $id)
	{
		$msg = $_REQUEST['sms'];
		$em = $this->getDoctrine()->getManager();
		$config = $this->getUser()->getGlobalOption()->getElectionConfig();
		$entity = $em->getRepository('ElectionBundle:ElectionMember')->findOneBy(array('electionConfig' => $config,'id'=>$id));
		if (!$entity) {
			throw $this->createNotFoundException('Unable to find ElectionMember entity.');
		}

		exit;

	}

	public function committeeAction(Request $request , $id)
	{
		$msg = $_REQUEST['sms'];
		$em = $this->getDoctrine()->getManager();
		$config = $this->getUser()->getGlobalOption()->getElectionConfig();
		$entity = $em->getRepository('ElectionBundle:ElectionMember')->findOneBy(array('electionConfig' => $config,'id'=>$id));
		if (!$entity) {
			throw $this->createNotFoundException('Unable to find ElectionMember entity.');
		}

		exit;

	}

	public function eventAction(Request $request , $id)
	{
		$msg = $_REQUEST['sms'];
		$em = $this->getDoctrine()->getManager();
		$config = $this->getUser()->getGlobalOption()->getElectionConfig();
		$entity = $em->getRepository('ElectionBundle:ElectionMember')->findOneBy(array('electionConfig' => $config,'id'=>$id));
		if (!$entity) {
			throw $this->createNotFoundException('Unable to find ElectionMember entity.');
		}

		exit;

	}

	public function votecenterAction(Request $request , $id)
	{
		$msg = $_REQUEST['sms'];
		$em = $this->getDoctrine()->getManager();
		$config = $this->getUser()->getGlobalOption()->getElectionConfig();
		$entity = $em->getRepository('ElectionBundle:ElectionMember')->findOneBy(array('electionConfig' => $config,'id'=>$id));
		if (!$entity) {
			throw $this->createNotFoundException('Unable to find ElectionMember entity.');
		}

		exit;

	}

}
