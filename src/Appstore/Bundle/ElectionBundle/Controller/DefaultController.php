<?php

namespace Appstore\Bundle\ElectionBundle\Controller;

use Appstore\Bundle\ElectionBundle\Entity\ElectionConfig;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
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
		$committees    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionCommittee')->getTypeBaseCommittee($config);
		$typeBaseCommittees    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionCommittee')->getLocationGroupBaseCommittee($config);
		$locationBaseCommittees    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionCommittee')->getLocationBaseCommittee($config);
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
			'typeBaseCommittees'       => $typeBaseCommittees,


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

}
