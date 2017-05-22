<?php

namespace Appstore\Bundle\InventoryBundle\Repository;
use Appstore\Bundle\InventoryBundle\Entity\DeliveryReturn;
use Doctrine\ORM\EntityRepository;

/**
 * DeliveryReturnRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DeliveryReturnRepository extends EntityRepository
{
    public function findWithSearch($branch,$data)
    {

        $startDate = isset($data['startDate'])  ? $data['startDate'].' 00:00:00' :'';
        $endDate =   isset($data['endDate'])  ? $data['endDate'].' 23:59:59' :'';

        $item = isset($data['item'])? $data['item'] :'';
        $qb = $this->createQueryBuilder('e');
        $qb->where("e.branch = :branch");
        $qb->setParameter('branch', $branch);

        if (!empty($startDate) and $startDate !="") {
            $qb->andWhere("e.created >= :startDate");
            $qb->setParameter('startDate', $startDate);
        }
        if (!empty($endDate)) {
            $qb->andWhere("e.created <= :endDate");
            $qb->setParameter('endDate', $endDate);
        }

        if (!empty($item)) {
            $qb->join('e.item', 'item');
            $qb->andWhere("item.sku = :sku");
            $qb->setParameter('sku', $item);
        }
        $qb->orderBy('e.id','DESC');
        $qb->getQuery();
        return  $qb;
    }

}
