<?php

namespace Appstore\Bundle\RestaurantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $option = $user->getGlobalOption();
        $salesOverview = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->findWithSalesOverview($this->getUser(),$data);
        $salesTransactionOverview = $em->getRepository('RestaurantBundle:Invoice')->todaySalesOverview($user,$data,'true');
        $previousSalesTransactionOverview = $em->getRepository('RestaurantBundle:Invoice')->todaySalesOverview($user,$data,'false');

        return $this->render('RestaurantBundle:Default:index.html.twig', array(
            'salesOverview' => $salesOverview,
            'salesTransactionOverview' => $salesTransactionOverview,
            'previousSalesTransactionOverview' => $previousSalesTransactionOverview,
            'option' => $option,
            'searchForm' => $data,
        ));

    }

}
