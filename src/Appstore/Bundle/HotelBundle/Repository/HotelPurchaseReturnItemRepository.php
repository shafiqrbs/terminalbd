<?php

namespace Appstore\Bundle\HotelBundle\Repository;
use Appstore\Bundle\HotelBundle\Entity\HotelParticular;
use Doctrine\ORM\EntityRepository;


/**
 * MedicinePurchaseReturnItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class HotelPurchaseReturnItemRepository extends EntityRepository
{


    public function purchaseReturnStockUpdate(HotelParticular $item)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.quantity) AS quantity');
        $qb->where('e.businessParticular = :businessParticular')->setParameter('businessParticular', $item->getId());
        $qnt = $qb->getQuery()->getOneOrNullResult();
        return $qnt['quantity'];
    }
}
