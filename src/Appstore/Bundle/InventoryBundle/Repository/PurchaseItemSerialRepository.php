<?php

namespace Appstore\Bundle\InventoryBundle\Repository;
use Appstore\Bundle\InventoryBundle\Entity\Item;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseItem;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseItemSerial;
use Doctrine\ORM\EntityRepository;


/**
 * PurchaseItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PurchaseItemSerialRepository extends EntityRepository
{



    public function insertPurchaseItemSerial(Purchase $purchase){

        $em = $this->_em;
        /* @var $item PurchaseItem  */

        foreach($purchase->getPurchaseItems() as $item){
            if(empty($item->getItemSerials()) and $item->getSerialNo()){
                $ids = explode(",", $item->getSerialNo());
                foreach ($ids as $id){
                    $entity = new PurchaseItemSerial();
                    $entity->setInventoryConfig($purchase->getInventoryConfig());
                    $entity->setPurchaseItem($item);
                    $entity->setBarcode($id);
                    $entity->setSerialNo($id);
                    $entity->setStatus(0);
                    $em->persist($entity);
                    $em->flush();
                }

            }

        }

    }

   public function returnPurchaseItemDetails($inventory,$barcode)
    {
        $qb = $this->createQueryBuilder('pis');
        $qb->select('pis');
        $qb->where("pis.inventoryConfig = :inventory")->setParameter('inventory', $inventory->getId());
        $qb->andWhere("pis.status != 1" );
        $qb->andWhere("pis.barcode = :barcode" )->setParameter('barcode', $barcode);
        $row = $qb->getQuery()->getOneOrNullResult();
        if($row){
            return $row;
        }
        return false;
    }

    public function getSerialProducts(Item $entity)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id  as id','e.barcode  as barcode','e.updated  as updated');
        $qb->where("e.item = :item");
        $qb->andWhere("e.status != 1");
        $qb->setParameter('item', $entity->getId());
        $result = $qb->getQuery()->getArrayResult();
        return $result;

    }



}
