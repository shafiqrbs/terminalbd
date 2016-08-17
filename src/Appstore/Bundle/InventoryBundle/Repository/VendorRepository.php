<?php
namespace Appstore\Bundle\InventoryBundle\Repository;
use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;

/**
 * VendorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class VendorRepository extends EntityRepository
{
    public function getLastId($inventory)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('count(e.id)');
        $qb->from('InventoryBundle:Vendor','e');
        $qb->where("e.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);
        $count = $qb->getQuery()->getSingleScalarResult();
        if($count > 0 ){
            return $count+1;
        }else{
            return 1;
        }

    }

    public function searchAutoComplete($q, InventoryConfig $inventory)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.inventoryConfig', 'ic');
        $query->select('e.companyName as id');
        $query->addSelect('e.companyName as text');
        $query->where($query->expr()->like("e.companyName", "'$q%'"  ));
        $query->andWhere("ic.id = :inventory");
        $query->setParameter('inventory', $inventory->getId());
        $query->groupBy('e.id');
        $query->orderBy('e.companyName', 'ASC');
        $query->setMaxResults( '30' );
        return $query->getQuery()->getResult();

    }

}
