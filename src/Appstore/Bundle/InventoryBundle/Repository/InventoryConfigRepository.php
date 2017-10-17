<?php

namespace Appstore\Bundle\InventoryBundle\Repository;
use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * InventoryConfigRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InventoryConfigRepository extends EntityRepository
{


    public function inventoryReset(GlobalOption $option)
    {

        $em = $this->_em;
        $config = $option->getInventoryConfig()->getId();

        $StockItem = $em->createQuery('DELETE InventoryBundle:StockItem e WHERE e.inventoryConfig = '.$config);
        $StockItem->execute();

        $Damage = $em->createQuery('DELETE InventoryBundle:Damage e WHERE e.inventoryConfig = '.$config);
        $Damage->execute();

        $ExcelImporter = $em->createQuery('DELETE InventoryBundle:ExcelImporter e WHERE e.inventoryConfig = '.$config);
        $ExcelImporter->execute();

        $SalesReturn = $em->createQuery('DELETE InventoryBundle:SalesReturn e WHERE e.inventoryConfig = '.$config);
        $SalesReturn->execute();

        $Delivery = $em->createQuery('DELETE InventoryBundle:Delivery e WHERE e.inventoryConfig = '.$config);
        $Delivery->execute();

        $Sales = $em->createQuery('DELETE InventoryBundle:Sales e WHERE e.inventoryConfig = '.$config);
        $Sales->execute();

        $SalesImport = $em->createQuery('DELETE InventoryBundle:SalesImport e WHERE e.inventoryConfig = '.$config);
        $SalesImport->execute();

        $PurchaseReturn = $em->createQuery('DELETE InventoryBundle:PurchaseReturn e WHERE e.inventoryConfig = '.$config);
        $PurchaseReturn->execute();

        $PurchaseVendorItem = $em->createQuery('DELETE InventoryBundle:PurchaseVendorItem e WHERE e.inventoryConfig = '.$config);
        $PurchaseVendorItem->execute();

        $Purchase = $em->createQuery('DELETE InventoryBundle:Purchase e WHERE e.inventoryConfig = '.$config);
        $Purchase->execute();

        $Purchase = $em->createQuery('DELETE InventoryBundle:Item e WHERE e.inventoryConfig = '.$config);
        $Purchase->execute();

        $Purchase = $em->createQuery('DELETE InventoryBundle:Product e WHERE e.inventoryConfig = '.$config);
        $Purchase->execute();

        $Purchase = $em->createQuery('DELETE InventoryBundle:ItemBrand e WHERE e.inventoryConfig = '.$config);
        $Purchase->execute();

    }


}
