<?php

namespace Appstore\Bundle\ElectionBundle\Controller;

use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
	public function indexAction()
	{

		/* @var GlobalOption $globalOption */

		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$datetime = new \DateTime("now");
		$data['startDate'] = $datetime->format('Y-m-d');
		$data['endDate'] = $datetime->format('Y-m-d');

		$user = $this->getUser();
		return $this->render('ElectionBundle:Default:index.html.twig', array(
			'globalOption'                    => $user->getGlobalOption() ,
		));
	}

}
