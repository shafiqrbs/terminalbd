<?php

namespace Appstore\Bundle\EcommerceBundle\Repository;
use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * OnlineOrderRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OrderRepository extends EntityRepository
{

    public function insertOrder(GlobalOption $globalOption)
    {
        $em = $this->_em;
        $order = new Order();
        $user = $em->getRepository('UserBundle:User')->find(30);
        $order->setCreatedBy($user);
        $order->setEcommerceConfig($globalOption->getEcommerceConfig());
        $em->persist($order);
        $em->flush();

        return $order;
    }
}
