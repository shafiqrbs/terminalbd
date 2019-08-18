<?php

namespace Appstore\Bundle\TallyBundle\Repository;
use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * BusinessConfigRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TallyConfigRepository extends EntityRepository
{


    public function businessReset(GlobalOption $option)
    {

	    set_time_limit(0);
	    ignore_user_abort(true);

	    $em = $this->_em;
	    $config = $option->getBusinessConfig()->getId();

	    $sales = $em->createQuery('DELETE BusinessBundle:BusinessInvoice e WHERE e.businessConfig = '.$config);
	    $sales->execute();

	    $purchase = $em->createQuery('DELETE BusinessBundle:BusinessPurchase e WHERE e.businessConfig = '.$config);
	    $purchase->execute();

	    $purchase = $em->createQuery('DELETE BusinessBundle:BusinessVendorStock e WHERE e.businessConfig = '.$config);
	    $purchase->execute();

	 //   $stock = $em->createQuery('DELETE BusinessBundle:BusinessParticular e WHERE e.businessConfig = '.$config);
	  //  $stock->execute();

	    $items = $this->_em->getRepository('BusinessBundle:BusinessParticular')->findBy(array('businessConfig'=>$config));

	    /* @var BusinessParticular $item */

	    foreach ($items as $item){

		    $stock = $em->createQuery('DELETE BusinessBundle:BusinessProductionExpense e WHERE e.productionItem = '.$item->getId());
		    $stock->execute();

		    $stock = $em->createQuery('DELETE BusinessBundle:BusinessProductionElement e WHERE e.businessParticular = '.$item->getId());
		    $stock->execute();

		    $stock = $em->createQuery('DELETE BusinessBundle:BusinessProduction e WHERE e.businessParticular = '.$item->getId());
		    $stock->execute();

		    $item->setQuantity(0);
		    $item->setPurchaseQuantity(0);
		    $item->setSalesQuantity(0);
		    $item->setSalesReturnQuantity(0);
		    $item->setPurchaseReturnQuantity(0);
		    $item->setDamageQuantity(0);
		    $item->setMinQuantity(0);
		    $item->setRemainingQuantity(0);
		    $item->setOpeningQuantity(0);
		    $this->_em->flush($item);
	    }

    }
}
