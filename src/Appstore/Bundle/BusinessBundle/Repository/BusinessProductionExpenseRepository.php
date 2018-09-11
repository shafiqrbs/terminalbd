<?php

namespace Appstore\Bundle\BusinessBundle\Repository;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessProductionElement;
use Doctrine\ORM\EntityRepository;


/**
 * ProductionElementRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BusinessProductionExpenseRepository extends EntityRepository
{
    public function removeProductionExpense(BusinessInvoice $invoice)
    {
        $em = $this->_em;

	    if(!empty($invoice->getBusinessInvoiceParticulars())) {

		    /* @var  $item BusinessInvoiceParticular */
		    foreach ($invoice->getBusinessInvoiceParticulars() as $item) {
			    $transaction = $em->createQuery( "DELETE BusinessBundle:BusinessProductionExpense e WHERE e.businessInvoiceParticular ={$item->getId()}");
			    $transaction->execute();
		    }

	    }
	}

}
