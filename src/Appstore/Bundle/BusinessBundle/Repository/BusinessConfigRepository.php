<?php

namespace Appstore\Bundle\BusinessBundle\Repository;
use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessStore;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * BusinessConfigRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BusinessConfigRepository extends EntityRepository
{


    public function businessReset(GlobalOption $option)
    {

        set_time_limit(0);
        ignore_user_abort(true);

        $em = $this->_em;
        $config = $option->getBusinessConfig()->getId();

        $ledger = $em->createQuery("DELETE BusinessBundle:BusinessStoreLedger e WHERE e.businessConfig = {$config}");
        $ledger->execute();

        //$store = $em->createQuery("DELETE BusinessBundle:BusinessStore e WHERE e.businessConfig = {$config}");
        //$store->execute();

        $history = $em->createQuery('DELETE BusinessBundle:BusinessStockHistory e WHERE e.businessConfig = '.$config);
        $history->execute();

        $DistributionReturnItem = $em->createQuery('DELETE BusinessBundle:BusinessDistributionReturnItem e WHERE e.businessConfig = '.$config);
        $DistributionReturnItem->execute();

        $PurchaseReturn = $em->createQuery('DELETE BusinessBundle:BusinessPurchaseReturn e WHERE e.businessConfig = '.$config);
        $PurchaseReturn->execute();

        $sales = $em->createQuery('DELETE BusinessBundle:BusinessInvoice e WHERE e.businessConfig = '.$config);
        $sales->execute();

	    $purchase = $em->createQuery('DELETE BusinessBundle:BusinessVendorStock e WHERE e.businessConfig = '.$config);
	    $purchase->execute();

	    $items = $this->_em->getRepository('BusinessBundle:BusinessParticular')->findBy(array('businessConfig'=>$config));

	    /* @var BusinessParticular $item */

	    foreach ($items as $item){

		    $bpx = $em->createQuery('DELETE BusinessBundle:BusinessProductionExpense e WHERE e.productionItem = '.$item->getId());
            $bpx->execute();

		    $bpe = $em->createQuery('DELETE BusinessBundle:BusinessProductionElement e WHERE e.businessParticular = '.$item->getId());
            $bpe->execute();

		    $bp = $em->createQuery('DELETE BusinessBundle:BusinessProduction e WHERE e.businessParticular = '.$item->getId());
            $bp->execute();

		    $ip = $em->createQuery('DELETE BusinessBundle:BusinessInvoiceParticular e WHERE e.businessParticular = '.$item->getId());
            $ip->execute();

		    $item->setQuantity(0);
		    $item->setPurchaseQuantity(0);
		    $item->setSalesQuantity(0);
		    $item->setSalesReturnQuantity(0);
		    $item->setPurchaseReturnQuantity(0);
		    $item->setDamageQuantity(0);
		    $item->setMinQuantity(0);
		    $item->setRemainingQuantity(0);
		    $item->setOpeningQuantity(0);
		    $item->setBonusQuantity(0);
		    $item->setBonusSalesQuantity(0);
		    $item->setBonusPurchaseQuantity(0);
		    $item->setOpeningApprove(0);
		    $this->_em->flush($item);
	    }

        $sales = $em->createQuery('DELETE BusinessBundle:BusinessInvoice e WHERE e.businessConfig = '.$config);
        $sales->execute();

        $items = $this->_em->getRepository('BusinessBundle:BusinessStore')->findBy(array('businessConfig'=>$config));

        /* @var BusinessStore $item */

        foreach ($items as $item){
            $item->setBalance(0);
            $this->_em->flush($item);
        }

    }
}
