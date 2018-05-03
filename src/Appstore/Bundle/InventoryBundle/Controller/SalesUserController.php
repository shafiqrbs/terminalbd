<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Appstore\Bundle\DomainUserBundle\Entity\DomainUser;
use Core\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\InventoryBundle\Entity\WareHouse;
use Appstore\Bundle\InventoryBundle\Form\WareHouseType;

/**
 * WareHouse controller.
 *
 */
class SalesUserController extends Controller
{

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);

        return $this->render('InventoryBundle:SalesUser:index.html.twig', array(
            'globalOption'  => $globalOption,
            'employees'     => $employees,
        ));
    }


    /**
     * Creates a new WareHouse entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $request->request->all();

        foreach ($data['user'] as $key => $value){
            $user = $this->getDoctrine()->getRepository('UserBundle:User')->find($value);
            $domainUser = $this->getDoctrine()->getRepository('DomainUserBundle:DomainUser')->findOneBy(array('user' => $user->getId()));
            if(!empty($domainUser)){
                $sales = $data['sales'][$key];
                $this->getDoctrine()->getRepository('DomainUserBundle:DomainUser')->updateSalesTarget($user,$sales);
            }else{
                $entity = new DomainUser();
                $entity->setGlobalOption($globalOption);
                $entity->setUser($user);
                $entity->setSales($data['sales'][$key]);
                $em->persist($entity);
                $em->flush();
            }

        }
        return $this->redirect($this->generateUrl('inventory_sales_user'));
    }



}
