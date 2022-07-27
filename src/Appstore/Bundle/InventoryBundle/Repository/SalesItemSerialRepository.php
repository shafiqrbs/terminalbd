<?php

namespace Appstore\Bundle\InventoryBundle\Repository;
use Appstore\Bundle\InventoryBundle\Entity\Item;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseItem;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseItemSerial;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Appstore\Bundle\InventoryBundle\Entity\SalesItem;
use Appstore\Bundle\InventoryBundle\Entity\SalesItemSerial;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * SalesItemSerialRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SalesItemSerialRepository extends EntityRepository
{

    public function insertSalesItemSerial($salesItem,PurchaseItemSerial $serial){

        $em = $this->_em;
        $exist = $this->findOneBy(array('purchaseItemSerial' => $serial));
        if(empty($exist)){
            $entity = new SalesItemSerial();
            $entity->setSalesItem($salesItem);
            $entity->setPurchaseItemSerial($serial);
            $entity->setBarcode($serial->getBarcode());
            $em->persist($entity);
            $serial->setStatus(1);
            $em->flush();
        }

    }

    public function deleteSalesItemSerial(SalesItem $salesItem){

        $em = $this->_em;
        /* @var $serial SalesItemSerial */
        if($salesItem->getSalesItemSerials()){
            foreach ($salesItem->getSalesItemSerials() as $serial) {
                $item = $serial->getPurchaseItemSerial();
                $item->setStatus(0);
            }
        }
    }

}
