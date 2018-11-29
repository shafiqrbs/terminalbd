<?php

namespace Appstore\Bundle\ElectionBundle\Controller;

use Appstore\Bundle\ElectionBundle\Entity\ElectionConfig;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{


	public function indexAction()
	{

		$em = $this->getDoctrine()->getManager();

		/* @var $config ElectionConfig */

		$config     = $this->getUser()->getGlobalOption()->getElectionConfig();
		$setup      = $config->getSetup();
		$members    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionMember')->getGenderBaseMember($config);
		$voters    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionMember')->getGenderBaseVoter($config);
		$unionMembers    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionMember')->getUnionWiseMember($config);
		$unionVoters    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionVoteCenter')->getUnionWiseVoter($config);
		$wardMembers    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionMember')->getWardWiseMember($config);
		$committees    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionCommittee')->getCommittees($setup);
		$typeBaseCommittees    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionCommittee')->getLocationGroupBaseCommittee($setup);
		$locationBaseCommittees    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionCommittee')->getLocationBaseCommittee($setup);
		$eventTypes    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionEvent')->getTypeBaseEvent($config);
		$locationBaseEvent    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionEvent')->getLocationBaseEvent($config);
		$campaignsType    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionCampaignAnalysis')->getAnalysisType($config);
		$priorities    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionCampaignAnalysis')->getPriorityBaseEvent($config);
		$unionBaseCenters    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionVoteCenter')->getUnionBaseVoteCenter($config);
		$events    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionEvent')->getEvents($config);
		$campaigns    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionCampaignAnalysis')->getCampaigns($config);

		return $this->render('ElectionBundle:Default:index.html.twig', array(

			'members'                   => $members,
			'voters'                    => $voters,
			'unionVoters'               => $unionVoters,
			'unionMembers'              => $unionMembers,
			'typeBaseCommittees'        => $typeBaseCommittees,
			'wardMembers'               => $wardMembers,
			'committees'                => $committees,
			'locationBaseCommittees'    => $locationBaseCommittees,
			'eventTypes'                => $eventTypes,
			'locationBaseEvent'         => $locationBaseEvent,
			'campaigns'                 => $campaigns,
			'campaignsType'             => $campaignsType,
			'priorities'                => $priorities,
			'unionBaseCenters'          => $unionBaseCenters,
			'events'                    => $events,
			'setup'                     => $setup,
			'globalOption'              => $this->getUser()->getGlobalOption(),

		));
	}

	public function voteCenterAction()
	{

		$data = $_REQUEST;
		$config = $this->getUser()->getGlobalOption()->getElectionConfig();
		$entities = $this->getDoctrine()->getRepository('ElectionBundle:ElectionVoteCenter')->findWithSearch($config,$data);
		$pagination = $entities->getQuery()->getResult();
		return $this->render('ElectionBundle:Report:voter-center.html.twig', array(
			'entities' => $pagination,
			'searchForm' => $data,
		));
	}

	public function voteCenterUnionAction()
	{

		/* @var $config ElectionConfig */

		$data = $_REQUEST;
		$config     = $this->getUser()->getGlobalOption()->getElectionConfig();
		$unionVoters    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionVoteCenter')->getUnionWiseVoter($config,$data);
		return $this->render('ElectionBundle:Report:voter-center-union.html.twig', array(

			'config'                   => $config,
			'entities'               => $unionVoters,
			'searchForm'            => $data,
			'globalOption'              => $this->getUser()->getGlobalOption(),

		));
	}

	public function voteCenterUnionPrintAction()
	{
		/* @var $config ElectionConfig */
		$data = $_REQUEST;
		$config     = $this->getUser()->getGlobalOption()->getElectionConfig();
		$unionVoters    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionVoteCenter')->getUnionWiseVoter($config,$data);
		return $this->render('ElectionBundle:Report/Print:voter-center-union.html.twig', array(

			'config'                   => $config,
			'entities'               => $unionVoters,
			'searchForm'            => $data,
			'globalOption'              => $this->getUser()->getGlobalOption(),

		));
	}

	public function voteCenterDetailsAction()
	{
		$data = $_REQUEST;
		$config = $this->getUser()->getGlobalOption()->getElectionConfig();
		return $this->render('ElectionBundle:Report:vote-center-details.html.twig', array(
			'searchForm' => $data,
		));
	}

	public function voteCenterDetailsPrintAction()
	{
		$data = $_REQUEST;
		$config = $this->getUser()->getGlobalOption()->getElectionConfig();
		$entity = $this->getDoctrine()->getRepository('ElectionBundle:ElectionVoteCenter')->findVoteCenter($config,$data);
		return $this->render('ElectionBundle:Report/Print:vote-center-details.html.twig', array(
			'entity' => $entity,
			'config' => $config,
			'searchForm' => $data,
		));
	}

	public function locationGroupMemberAction()
	{

	}

	public function locationBaseCommitteeAction()
	{

	}

	public function locationTypeBaseCommitteeAction()
	{

	}

	public function committeeListAction()
	{

	}

	public function committeeDetailsAction()
	{

	}

	public function electionResultAction()
	{

	}

	public function voteCenterListAction()
	{

	}


	public function campaignListAction()
	{

	}

	public function campaignDetailsAction()
	{

	}

	public function analysisListAction()
	{

	}

	public function analysisDetailsAction()
	{

	}



}
