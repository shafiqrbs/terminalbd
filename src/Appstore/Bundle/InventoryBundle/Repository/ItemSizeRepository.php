<?php

namespace Appstore\Bundle\InventoryBundle\Repository;
use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem;
use Doctrine\ORM\EntityRepository;

/**
 * ItemSizeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ItemSizeRepository extends EntityRepository
{
    public function getLastId($inventory)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('count(cs)');
        $qb->from('InventoryBundle:ItemSize','cs');
        $qb->where("cs.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);
        $count = $qb->getQuery()->getSingleScalarResult();
        if($count > 0 ){
            return $count+1;
        }else{
            return 1;
        }

    }

    public function searchAutoComplete($q)
    {
        $query = $this->createQueryBuilder('e');
        $query->select('e.name as id');
        $query->addSelect('e.name as text');
        $query->where('e.status=1');
        $query->andWhere($query->expr()->like("e.name", "'$q%'"  ));
        $query->groupBy('e.id');
        $query->orderBy('e.name', 'ASC');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getResult();

    }

    public function getCategoryBaseSize(PurchaseVendorItem $entity)
    {
        $query = $this->createQueryBuilder('e');
        //$query->join('e.category', 'category');
        $query->where("e.status = 1");
        $query->andWhere("e.inventoryConfig = :inventory");
        $query->setParameter('inventory', $entity->getInventoryConfig()->getId());
        //$query->andWhere("category.id = :catId");
        //$query->setParameter('catId', $entity->getCategory()->getId());
        $query->orderBy('e.name', 'ASC');
        return $query->getQuery()->getResult();

    }

    public function getGroupSizes($inventoryConfig, $array = array() ){

        $query = $this->createQueryBuilder('e');
        $query->join('e.sizeGroup','sizegroup');
        $query->select('e.id as id');
        $query->addSelect('e.name as name');
        $query->where('e.status=1');
        $query->andWhere('sizegroup.inventoryConfig='.$inventoryConfig->getId());
        $query->orderBy('e.name', 'ASC');
        $sizes = $query->getQuery()->getResult();

        $value ='';
        $value .='<ul class="ul-check-list">';
        foreach ($sizes as $val) {
            $checkd = in_array($val['id'], $array)? 'checked':'';
            $value .= '<li><input type="checkbox" class="checkbox" '.$checkd.' name="brand[]" value="'.$val['id'].'" ><span class="label">'.$val['name']. '</span></li>';
        }
        $value .='</ul>';
        return $value;

    }
}
