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
		$members    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionMember')->getUnionWiseMember($config);
		$members    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionMember')->getWardWiseMember($config);
		$members    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionCommittee')->getTypeBaseCommittee($config);
		$members    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionCommittee')->getLocationBaseCommittee($config);
		$members    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionEvent')->getTypeBaseEvent($config);
		$members    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionEvent')->getLocationBaseEvent($config);
		$members    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionCampaignAnalysis')->getAnalysisBaseEvent($config);
		$members    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionCampaignAnalysis')->getPriorityBaseEvent($config);
		$members    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionCampaignAnalysis')->getLocationBaseEvent($config);
		$members    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionCampaignAnalysis')->getLocationBaseEvent($config);
		$members    = $this->getDoctrine()->getRepository('ElectionBundle:ElectionVoteCenter')->getUnionBaseVoteCenter($config);
		var_dump($members);
		exit;

		return $this->render('ElectionBundle:Default:index.html.twig', array(

			'members'               => $members,
			'setup'                 => $setup,
			'globalOption'          => $this->getUser()->getGlobalOption(),


		));
	}

}
